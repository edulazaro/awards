<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

use App\Models\User;
use App\Models\Reward;
use App\Events\AchievementUnlocked;
use App\Awards\AchievementsBadge;

class BadgeAwardTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Test that the beginner badge is awarded.
     */
    public function test_the_beginner_badge_is_awarded_on_register(): void
    {
        $user = User::factory()->create();

        $beginnerBadgeExists = $user->rewards()->where([
            'award_type' => 'badge',
            'name' => 'beginner'
        ])->exists();

        $this->assertEquals(true, $beginnerBadgeExists);
    }

    /**
     * Test that right badges are awarded for each tier
     */
    public function test_the_right_badges_are_awarded_when_unlocking_achievements(): void
    {
        $user = User::factory()->create();

        $tiers = AchievementsBadge::scope($user)->tiers();

        $rewardsCount = 1;
        foreach ($tiers as $tierName => $tier) {
            $rewards = Reward::factory()->count($tier['score'] - $rewardsCount)->create([
                'awardable_id' => $user->id,
                'awardable_type' => 'user',
                'award_id' => 'comments_achievement',
                'award_type' => 'achievement'
            ]);

            $rewards->each(function ($reward) {
                AchievementUnlocked::dispatch($reward);
            });

            $rewardsCount = $user->rewards()->where('award_type', 'achievement')->count();
            $this->assertEquals($tier['score'], $rewardsCount);

            $this->assertTrue($user->rewards()->where('name', $tierName)->exists());
        }
    }
}
