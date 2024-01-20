<?php

namespace App\Models;

use App\Enums\BadgeRequirementsEnum;
use App\Enums\BadgesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Badge extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function nextFor(User $user): ?string
    {
        $currentBadge = $user->badge->name;

        $currentBadgeIndex = array_search($currentBadge, BadgesEnum::toArray(), true);
        if ($currentBadgeIndex !== false && $currentBadgeIndex < count(BadgesEnum::toArray()) - 1) {
            return BadgesEnum::toArray()[$currentBadgeIndex + 1];
        }

        return null;
    }

    public static function remainingToUnlockNext(User $user): int
    {
        $achievementsCount = $user->achievements->count();

        $nextBadge = self::nextFor($user);
        if ($nextBadge) {
            return BadgeRequirementsEnum::getValue($nextBadge) - $achievementsCount;
        }

        return 0;
    }
}
