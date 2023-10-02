<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use App\Awards\CommentsAchievement;

class CommentWritten
{
    use Dispatchable, SerializesModels;

    public $award;
    public $comment;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        $this->user = $comment->user;
    }
}
