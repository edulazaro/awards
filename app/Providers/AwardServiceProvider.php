<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

use App\Models\User;

use App\Awards\AchievementsBadge;
use App\Awards\CommentsAchievement;
use App\Awards\LessonsWatchedAchievement;

use App\Support\Collections\Awards;

class AwardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Awards::enforceMap([
            'comments_achievement' => CommentsAchievement::class,
            'lessons_watched_achievement' => LessonsWatchedAchievement::class,
            'achievements_badge' => AchievementsBadge::class
        ]);

        User::awardableGroup('achievements', [
            CommentsAchievement::class,
            LessonsWatchedAchievement::class
        ]);

        User::awardable(AchievementsBadge::class);
    }
}


