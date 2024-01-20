<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\BadgeRequirementsEnum;
use App\Enums\BadgesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function badge()
    {
        return $this->hasOne(Badge::class);
    }

    public function unlockAchievement(string $achievementName): void
    {
        if (!$this->hasAchievement($achievementName)) {
            $this->achievements()->create(['name' => $achievementName]);
        }
    }

    public function hasAchievement(string $achievementName): bool
    {
        return $this->achievements()->where('name', $achievementName)->exists();
    }

    public function unlockBadge(string $badgeName): void
    {
        if (!$this->hasBadge($badgeName)) {
            $this->badge()->create(['name' => $badgeName]);
        }
    }

    public function hasBadge(string $badgeName): bool
    {
        return $this->badge()->where('name', $badgeName)->exists();
    }

    public static function nextAchievementFor(User $user): ?string
    {
        $unlockedAchievements = $user->achievements->pluck('name');
        $achievementsListArray = Achievement::all()->toArray();

        foreach ($achievementsListArray as $achievement) {
            if (!$unlockedAchievements->contains($achievement)) {
                return $achievement;
            }
        }

        return null;
    }

    public function nextBadgeFor(): ?string
    {
        $currentBadge = $this->badge->name;
        $badgesListArray = Badge::all()->toArray();

        $currentBadgeIndex = array_search($currentBadge, $badgesListArray, true);
        if ($currentBadgeIndex !== false && $currentBadgeIndex < count($badgesListArray) - 1) {
            return $badgesListArray[$currentBadgeIndex + 1];
        }

        return null;
    }

    public function remainingToUnlockNext(): int
    {
        $achievementsCount = $this->achievements->count();

        $nextBadge = $this->nextBadgeFor();
        if ($nextBadge) {
            return BadgeRequirementsEnum::getValue($nextBadge) - $achievementsCount;
        }

        return 0;
    }
}

