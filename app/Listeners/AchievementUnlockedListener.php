<?php

namespace App\Listeners;

use App\Events\AccessCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\AchievementUnlocked;

class AchievementUnlockedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\AccessCreatedEvent  $event
     * @return void
     */
    public function handle(AchievementUnlocked $event)
    {

    }
}
