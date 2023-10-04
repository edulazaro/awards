<?php

namespace App\Awards;

use App\Collections\Awards;

use App\Concerns\IsAward;
use App\Concerns\HasRewards;
use App\Contracts\AwardInterface;
use App\Events\BadgeUnlocked;

class AchievementsBadge implements AwardInterface
{
    use IsAward;

    /** @var string The award internal name. */
    public string $id = 'comment_written';

    /** @var string The award type. */
    public string $type = 'badge';
    
    /** @var string The award title. */
    protected string $title = 'Comment Written';

    /** @var string The success event. */
    protected string $event = BadgeUnlocked::class;

    /** @var array The award tiers. */
    protected array $tiers = [
        'beginner' => [
            'score' => 0,
            'title' => 'Welcome',
        ],
        'intermediate' => [
            'score' => 4,
            'title' => '4 Achievements',
        ],
        'advanced' => [
            'score' => 8,
            'title' => '8 Achievements',
        ],
        'master' => [
            'score' => 10,
            'title' => '10 Achievements',
        ],
    ];

    /**
     * Get the awardable score a user
     *
     * @param $awardable;
     * @return int
     */
    public function score($awardable): int
    {
        return $awardable->rewards()->where('award_type', 'achievement')->count();
    }
}