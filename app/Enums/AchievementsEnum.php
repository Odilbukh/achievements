<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self firstLessonWatched()
 * @method static self fiveLessonsWatched()
 * @method static self tenLessonsWatched()
 * @method static self twentyFiveLessonsWatched()
 * @method static self fiftyLessonsWatched()
 * @method static self firstCommentWritten()
 * @method static self threeCommentsWritten()
 * @method static self fiveCommentsWritten()
 * @method static self tenCommentsWritten()
 * @method static self twentyCommentsWritten()
 */
final class AchievementsEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'firstLessonWatched' => 'First Lesson Watched',
            'fiveLessonsWatched' => '5 Lessons Watched',
            'tenLessonsWatched' => '10 Lessons Watched',
            'twentyFiveLessonsWatched' => '25 Lessons Watched',
            'fiftyLessonsWatched' => '50 Lessons Watched',
            'firstCommentWritten' => 'First Comment Written',
            'threeCommentsWritten' => '3 Comments Written',
            'fiveCommentsWritten' => '5 Comments Written',
            'tenCommentsWritten' => '10 Comments Written',
            'twentyCommentsWritten' => '20 Comments Written',
        ];
    }
}
