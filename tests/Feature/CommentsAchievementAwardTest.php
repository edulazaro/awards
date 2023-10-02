<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

use App\Models\User;
use App\Models\Comment;


use App\Events\CommentWritten;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;

use App\Awards\CommentsAchievement;

class CommentsAchievementAwardTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Test the comment written achievement is not awarded with no comments
     */
    public function test_the_comment_written_achievement_is_not_awarded_with_no_comments(): void
    {
        $user = User::factory()->create();

        $achievementAwarded = $user->rewards()->where('name', 'comment_written')->exists();
        $this->assertFalse($achievementAwarded);
    }

    /**
     * Test the comment written achievement is awarded with a comment
     */
    public function test_the_first_comment_written_achievement_is_awarded(): void
    {
        $user = User::factory()->create();

        $comment = $user->comments()->create([
            'body' => 'A body',
        ]);

        Event::fake([
            AchievementUnlocked::class
        ]);

        CommentWritten::dispatch($comment);

        $commentsCount = $user->comments()->get()->count();
        $this->assertEquals(1, $commentsCount);

        $achievementAwarded = $user->rewards()->where('name', 'comment_written')->exists();
        $this->assertEquals(true, $achievementAwarded);

        Event::assertDispatched(AchievementUnlocked::class);
    }

    /**
     * Thest that the comment achievements are awarded according to the number of comments
     */
    public function test_the_comments_written_achievements_are_awarded_according_to_the_tiers(): void
    {
        $user = User::factory()->create();

        $tiers = CommentsAchievement::scope($user)->tiers();

        $commentsCount = 0;
        foreach ($tiers as $tierName => $tier) {
            $comments = Comment::factory()->withUser($user)->count($tier['score'] - $commentsCount)->create();

            $comments->each(function ($comment) {
                CommentWritten::dispatch($comment);
            });

            $commentsCount = $user->comments()->count();
            $this->assertEquals($tier['score'], $commentsCount);
            
            // The reward was correctly added
            $achievementsAwarded = $user->rewards()->where('name', $tierName)->exists();
            $this->assertTrue($achievementsAwarded);
        }
    }
}
