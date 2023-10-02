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

class BadgeUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $badge_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Reward $award)
    {
        $this->user = $award->awardable;
        $this->badge_name = $award->name;
    }
}
