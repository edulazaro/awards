<?php

namespace App\Observers;

use App\Models\User;
use App\Awards\AchievementsBadge;

class UserObserver
{
    /**
     * Handle the Reward "created" event.
     * 
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        AchievementsBadge::scope($user)->check();
    }
}
