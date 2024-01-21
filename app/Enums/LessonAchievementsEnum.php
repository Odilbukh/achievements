<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self firstLessonWatched()
 * @method static self fiveLessonsWatched()
 * @method static self tenLessonsWatched()
 * @method static self twentyFiveLessonsWatched()
 * @method static self fiftyLessonsWatched()
 */
final class LessonAchievementsEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'firstLessonWatched' => 1,
            'fiveLessonsWatched' => 5,
            'tenLessonsWatched' => 10,
            'twentyFiveLessonsWatched' => 25,
            'fiftyLessonsWatched' => 50,
        ];
    }
}
