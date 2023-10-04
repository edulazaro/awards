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
     * @param Reward $reward
     * @return void
     */
    public function __construct(Reward $reward)
    {
        $this->user = $reward->awardable;
        $this->achievement_name = $reward->name;
    }
}
