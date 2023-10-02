<?php

namespace App\Awards;

use App\Collections\Awards;

use App\Concerns\IsAward;
use App\Concerns\HasRewards;
use App\Contracts\AwardInterface;

use App\Events\AchievementUnlocked;

class LessonsWatchedAchievement implements AwardInterface
{
    use IsAward;

    /** @var string The award internal name. */
    public $id = 'lesson_watched';
    
    /** @var string The award type. */
    public $type = 'achievement';
    
    /** @var string The award title. */
    protected $title = 'Lesson Watched';

    protected $event = AchievementUnlocked::class;

    /** @var protected The award tiers. */
    protected array $tiers = [
        'lesson_watched' => [
            'score' => 1,
            'title' => 'First Lesson Watched',
        ],
        '5_lessons_watched' => [
            'score' => 5,
            'title' => '5 Lessons Watched',
        ],
        '10_lessons_watched' => [
            'score' => 10,
            'title' => '10 Lessons Watched',
        ],
        '25_lessons_watched' => [
            'score' => 25,
            'title' => '25 Lessons Watched',
        ],
        '50_lessons_watched' => [
            'score' => 50,
            'title' => '50 Lessons Watched',
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
        return $awardable->watched()->count();
    }
}