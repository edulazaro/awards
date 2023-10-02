<?php

namespace App\Awards;

use App\Collections\Awards;

use App\Concerns\IsAward;
use App\Concerns\HasRewards;
use App\Contracts\AwardInterface;

use App\Events\AchievementUnlocked;

class CommentsAchievement implements AwardInterface
{
    use IsAward;

    /** @var string The award internal name. */
    public $id = 'comments_written';
    
    /** @var string The award type. */
    public $type = 'achievement';
    
    /** @var string The award title. */
    protected $title = 'Comment Written';

    protected $event = AchievementUnlocked::class;

    /** @var protected The award tiers. */
    protected array $tiers = [
        'comment_written' => [
            'score' => 1,
            'title' => 'First Comment Written',
        ],
        '3_comments_written' => [
            'score' => 3,
            'title' => '3 Comments Written',
        ],
        '5_comments_written' => [
            'score' => 5,
            'title' => '5 Comments Written',
        ],
        '10_comments_written' => [
            'score' => 10,
            'title' => '10 Comments Written',
        ],
        '20_comments_written' => [
            'score' => 20,
            'title' => '20 Comments Written',
        ],
    ];

    /**
     * Get the awardable score a user
     *
     * @param $awardable;
     * @return int
     */
    public function score($awardable = null): int
    {
        return $awardable->comments()->count();
    }
}