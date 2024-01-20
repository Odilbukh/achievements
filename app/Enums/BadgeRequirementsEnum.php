<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self beginner()
 * @method static self intermediate()
 * @method static self advanced()
 * @method static self master()
 */
final class BadgeRequirementsEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'beginner' => 0,
            'intermediate' => 4,
            'advanced' => 8,
            'master' => 10,
        ];
    }
}
