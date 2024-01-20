<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self beginner()
 * @method static self intermediate()
 * @method static self advanced()
 * @method static self master()
 */
final class BadgesEnum extends Enum
{
    protected static function values(): array
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'master' => 'Master',
        ];
    }
}
