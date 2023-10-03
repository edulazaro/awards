<?php

namespace App\Awards;

use App\Concerns\IsAward;
use App\Concerns\HasRewards;
use App\Contracts\AwardInterface;

class MyOtherAward2 implements AwardInterface
{
    use IsAward;

    /** @var string The award internal name. */
    public $id = 'my_other_award2';

    /** An event to trigger when the award is rewarded. */
    // protected $event = AnEvent::class;

    /** @var protected The award tiers. */
    protected array $tiers = [
        'starter' => [
            'score' => 0,
            'title' => 'Welcome!',
        ],
        'intermediate' => [
            'score' => 2,
            'title' => 'Welcome Again!',
        ],
    ];

    /**
     * Returns the score for the award
     *
     * @param $awardable;
     * @return int
     */
    public function score($awardable = null): int
    {
        if ($awardable == null && $this->hasRewards) {
            $awardable = $this->hasRewards;
        }

        return 1;
    }
}