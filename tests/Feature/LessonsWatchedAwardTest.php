<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonUser;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;

use App\Awards\LessonsWatchedAchievement;

class LessonsWatchedAwardTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Test the lesson written achievement is not awarded with no watched lessons
     */
    public function test_the_lesson_watched_achievement_is_not_awarded_with_no_lessons_watched(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $achievementAwarded = $user->rewards()->where('name', 'lesson_watched')->exists();
        $this->assertFalse($achievementAwarded);
    }

    /**
     * Test the lesson watched achievement is awarded with a lesson watched
     */

    public function test_the_first_lesson_watched_achievement_is_awarded(): void
    {
        $this->seed();

        $user = User::factory()->create();

        $lessonWatched = LessonUser::factory()->withUser($user->id)->create();

        Event::fake([
            AchievementUnlocked::class
        ]);

        LessonWatched::dispatch($lessonWatched->lesson, $lessonWatched->user);

        $lessonsWatchedCount = $user->watched()->count();

        $this->assertEquals(1, $lessonsWatchedCount);

        $achievementAwarded = $user->rewards()->where('name', 'lesson_watched')->exists();
        $this->assertEquals(true, $achievementAwarded);

        Event::assertDispatched(AchievementUnlocked::class);
    }

    /**
     * Thest that the lesson achievements are awarded according to the number of lessons watched
     */

    public function test_the_lessons_watched_achievements_are_awarded_according_to_the_tiers(): void
    {
        $this->seed();

        $user = User::factory()->create();

        $tiers = LessonsWatchedAchievement::scope($user)->tiers();

        Event::fake([
            AchievementUnlocked::class
        ]);

        $lessonsWatchedCount = 0;
        foreach ($tiers as $tierName => $tier) {

            $lessonsWatched = LessonUser::factory()->withUser($user->id)
                              ->count($tier['score'] - $lessonsWatchedCount)->create();

            $lessonsWatched->each(function ($lessonWatched) use ($user) {
                LessonWatched::dispatch($lessonWatched->lesson, $user);
            });

            // The right event is dispached
            Event::assertDispatched(function (AchievementUnlocked $event) use ($tierName, $user) {
                return $event->achievement_name == $tierName;
            });
          
            $lessonsWatchedCount = $user->watched()->count();
            $this->assertEquals($tier['score'], $lessonsWatchedCount);
            
            // The reward was correctly added
            $achievementsAwarded = $user->rewards()->where('name', $tierName)->exists();
            $this->assertTrue($achievementsAwarded);        
        }
    }
}
