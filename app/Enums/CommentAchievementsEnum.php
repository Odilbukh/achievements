<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self firstCommentWritten()
 * @method static self threeCommentsWritten()
 * @method static self fiveCommentsWritten()
 * @method static self tenCommentsWritten()
 * @method static self twentyCommentsWritten()
 */
final class CommentAchievementsEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'firstCommentWritten' => 1,
            'threeCommentsWritten' => 3,
            'fiveCommentsWritten' => 5,
            'tenCommentsWritten' => 10,
            'twentyCommentsWritten' => 20,
        ];
    }
}
