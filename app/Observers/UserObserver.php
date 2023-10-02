<?php

namespace App\Observers;

use App\Models\User;
use App\Awards\AchievementsBadge;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        AchievementsBadge::scope($user)->check();
    }
}
