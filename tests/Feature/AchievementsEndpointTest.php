<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

use App\Models\User;
use App\Models\Reward;
use App\Models\Comment;
use App\Models\LessonUser;
use App\Events\LessonWatched;
use App\Events\AchievementUnlocked;
use App\Awards\LessonsWatchedAchievement;
use App\Events\CommentWritten;
use App\Awards\AchievementsBadge;
use App\Awards\CommentsAchievement;

class AchievementsEndpointTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Test that no comment achievement is under the unlocked_achievements key.
     */
    public function test_achievements_endpoint_no_comments(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $commentsAchievementNames = CommentsAchievement::scope($user)->tiers()->pluck('title');

        // 200 response
        $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
            'Accept' => 'application/json'
        ])->assertStatus(200);

        foreach( $commentsAchievementNames as  $commentsAchievementName) {
            $this->assertFalse(in_array($commentsAchievementName, $result['unlocked_achievements']));
        }
    }

    /**
     * Test that no lesson watched achievement is under the unlocked_achievements key.
     */
    public function test_achievements_no_lessons_watched(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $lessonsWatchedAchievementNames = LessonsWatchedAchievement::scope($user)->tiers()->pluck('title');

        // 200 response
        $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
            'Accept' => 'application/json'
        ])->assertStatus(200);

        foreach( $lessonsWatchedAchievementNames as  $lessonWatchedAchievementName) {
            $this->assertFalse(in_array($lessonWatchedAchievementName, $result['unlocked_achievements']));
        }
    }

    /**
     * Test that the comment achievements are awarded
     */
    public function test_achievements_endpoint_has_comments(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $tiers = CommentsAchievement::scope($user)->tiers();

        $commentsCount = 0;
        foreach ($tiers as $tierName => $tier) {
            $comments = Comment::factory()->withUser($user)->count($tier['score'] - $commentsCount)->create();

            $comments->each(function ($comment) {
                CommentWritten::dispatch($comment);
            });

            // 200 response
            $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
                'Accept' => 'application/json'
            ])->assertStatus(200);

            $this->assertTrue(in_array($tier['title'], $result['unlocked_achievements']));
        }
    }

    /**
     * Test that the lesson watched achievements are awarded
     */
    public function test_achievements_has_lessons_watched(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $tiers = LessonsWatchedAchievement::scope($user)->tiers();

        $lessonsWatchedCount = 0;
        foreach ($tiers as $tierName => $tier) {

            $lessonsWatched = LessonUser::factory()->withUser($user->id)
                              ->count($tier['score'] - $lessonsWatchedCount)->create();

            $lessonsWatched->each(function ($lessonWatched) use ($user) {
                LessonWatched::dispatch($lessonWatched->lesson, $user);
            });

            $lessonsWatchedCount = $user->watched()->count();
    
            // 200 response
            $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
                'Accept' => 'application/json'
            ])->assertStatus(200);

            $this->assertTrue(in_array($tier['title'], $result['unlocked_achievements']));
        }
    }

    /**
     * Test the next tier
     */
    public function test_comment_achievements_next_tier(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $tiers = CommentsAchievement::scope($user)->tiers();

        $commentsCount = 1;
        $iterationCount = 0;
        foreach ($tiers as $tierName => $tier) {
            $comments = Comment::factory()->withUser($user)->count($tier['score'] - $commentsCount)->create();

            $comments->each(function ($comment) {
                CommentWritten::dispatch($comment);
            });

            $commentsCount = $user->comments()->count();

            // 200 response
            $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
                'Accept' => 'application/json'
            ])->assertStatus(200);

            $nextTier = CommentsAchievement::scope($user)->nextTier();

            if (empty($iterationCount == count($tiers))) continue;
            $this->assertTrue(in_array($nextTier['title'], $result['next_available_achievements']));

            $iterationCount++;
        }
    }

    /**
     * Test the next tier
     */
    public function test_lesson_achievements_next_tier(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $tiers = LessonsWatchedAchievement::scope($user)->tiers();

        $lessonsWatchedCount = 0;
        $iterationCount = 0;
        foreach ($tiers as $tierName => $tier) {

            $lessonsWatched = LessonUser::factory()->withUser($user->id)
                              ->count($tier['score'] - $lessonsWatchedCount)->create();

            $lessonsWatched->each(function ($lessonWatched) use ($user) {
                LessonWatched::dispatch($lessonWatched->lesson, $user);
            });
    
            // 200 response
            $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
                'Accept' => 'application/json'
            ])->assertStatus(200);

            $nextTier = LessonsWatchedAchievement::scope($user)->nextTier();

            if (empty($iterationCount == count($tiers))) continue;
            $this->assertTrue(in_array($nextTier['title'], $result['next_available_achievements']));

            $iterationCount++;
        }
    }

    /**
     * Test that only the beginner badge is warded when there are no achievements
     */
    public function test_achievements_endpoint_beginner_badge_when_no_achievements(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $achievementsBadges = AchievementsBadge::scope($user)->tiers();

        // 200 response
        $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
            'Accept' => 'application/json'
        ])->assertStatus(200);

        foreach( $achievementsBadges as  $achievementsBadge) {
            if (!$achievementsBadge['score']) {
                $this->assertTrue($achievementsBadge['title'] == $result['current_badge']);
            } else {
                $this->assertFalse($achievementsBadge['title'] == $result['current_badge']);
            }
        }
    }

    /**
     * Test that the comment achievements are awarded
     */
    public function test_achievements_endpoint_has_correct_badge(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $tiers = AchievementsBadge::scope($user)->tiers();

        $achievementsCount = 0;
        $commentAchievementsCount = 0;
        $lessonAchievementsCount = 0;
        foreach ($tiers as $tierName => $tier) {

            $toCreate = $tier['score'] - $achievementsCount;
            while ($toCreate > 0) {

                if ($commentAchievementsCount < 5) {
                    $reward = Reward::factory()->withUser($user->id)
                    ->withAward(CommentsAchievement::scope($user))
                    ->create();
                    $commentAchievementsCount++;
                } else {
                    $reward = Reward::factory()->withUser($user->id)
                    ->withAward(LessonsWatchedAchievement::scope($user))
                    ->create();
                    $lessonAchievementsCount++;
                }

                $toCreate--;
            }

            $achievementsCount = $user->rewards()->where('award_type', 'achievement')->count();

            $badges = $user->rewards()->where('award_type', 'badge')->get();

            // 200 response
            $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
                'Accept' => 'application/json'
            ])->assertStatus(200);

            $this->assertTrue($tier['title'] == $result['current_badge']);
        }
    }

    /**
     * Test the next tier
     */
    public function test_achievements_badge_next_tier(): void
    {
        $this->seed();
        $user = User::factory()->create();

        $tiers = AchievementsBadge::scope($user)->tiers();

        $achievementsCount = 0;
        $commentAchievementsCount = 0;
        $lessonAchievementsCount = 0;
        $iterationCount = 0;
        foreach ($tiers as $tierName => $tier) {

            $toCreate = $tier['score'] - $achievementsCount;
            while ($toCreate > 0) {

                if ($commentAchievementsCount < 5) {
                    $reward = Reward::factory()->withUser($user->id)
                    ->withAward(CommentsAchievement::scope($user))
                    ->create();
                    $commentAchievementsCount++;
                } else {
                    $reward = Reward::factory()->withUser($user->id)
                    ->withAward(LessonsWatchedAchievement::scope($user))
                    ->create();
                    $lessonAchievementsCount++;
                }

                $toCreate--;
            }

            $achievementsCount = $user->rewards()->where('award_type', 'achievement')->count();

            $badges = $user->rewards()->where('award_type', 'badge')->get();

            // 200 response
            $result = $this->json('GET', '/users/' . $user->id . '/achievements', [
                'Accept' => 'application/json'
            ])->assertStatus(200);

            $nextTier = AchievementsBadge::scope($user)->nextTier();

            if (empty($iterationCount == count($tiers))) continue;
            $this->assertTrue(in_array($nextTier['title'], $result['next_available_achievements']));
            $iterationCount++;
        }
    }
}
