<?php

namespace App\Subscribers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;

use App\Events\LessonWatched;
use App\Events\CommentWritten;
use App\Events\AchievementUnlocked;

use App\Awards\LessonsWatchedAchievement;
use App\Awards\CommentsAchievement;
use App\Awards\AchievementsBadge;

class CheckAwards
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher  $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            CommentWritten::class,
            function($event): void {
                CommentsAchievement::scope($event->user)->check();
            }
        );

        $events->listen(
            LessonWatched::class,
            function($event): void {
                LessonsWatchedAchievement::scope($event->user)->check();
            }
        );

        $events->listen(
            AchievementUnlocked::class,
            function($event): void {
                AchievementsBadge::scope($event->user)->check();
            }
        );
    }
}
