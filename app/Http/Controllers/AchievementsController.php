<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $unlockedAchievements = $user->rewards()->where('award_type', 'achievement')
                                ->get()->pluck('title');

        $nextAvailableAchievements = $user->awardables()->where('type', 'achievement')
                                     ->nextUnlockableTiers()->pluck('title');

        $currentBadge = $user->rewards()->where('award_type', 'badge')->orderBy('id', 'DESC')->first();
    
        $nextBadge = $user->awardables()->where('type', 'badge')
                              ->nextUnlockableTiers()->pluck('title')->first();

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge ? $currentBadge->title : null,
            'next_badge' =>  $nextBadge,
            'remaing_to_unlock_next_badge' => $currentBadge ? $currentBadge->next_tier_score : null,
        ]);
    }
}
