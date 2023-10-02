<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Reward;

class AchievementUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $achievement_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Reward $award)
    {
        $this->user = $award->awardable;
        $this->achievement_name = $award->name;
    }
}
