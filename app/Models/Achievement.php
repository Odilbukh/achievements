<?php

namespace App\Models;

use App\Enums\AchievementsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function nextAvailableFor(User $user): ?string
    {
        $unlockedAchievements = $user->achievements->pluck('name');

        foreach (AchievementsEnum::toArray() as $achievement) {
            if (!$unlockedAchievements->contains($achievement)) {
                return $achievement;
            }
        }

        return null;
    }

}
