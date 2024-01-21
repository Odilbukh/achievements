<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\BadgeRequirementsEnum;
use App\Enums\BadgesEnum;
use App\Enums\CommentAchievementsEnum;
use App\Enums\LessonAchievementsEnum;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        'badge_id'
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
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievement_user', 'user_id', 'achievement_id');
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function unlockLessonAchievement(): void
    {
        $watchedLessonsCount = $this->watched->count();

        foreach (LessonAchievementsEnum::toArray() as $threshold => $achievementName) {
            if ($watchedLessonsCount >= $threshold && !$this->hasAchievement($achievementName, $this->id)) {
                $this->setAchievement($achievementName, $this->id);
            }
        }
    }

    public function unlockCommentAchievement(): void
    {
        $writtenCommentsCount = $this->comments->count();

        foreach (CommentAchievementsEnum::toArray() as $threshold => $achievementName) {
            if ($writtenCommentsCount >= $threshold && !$this->hasAchievement($achievementName, $this->id)) {
                $this->setAchievement($achievementName, $this->id);
            }
        }
    }

    public function getAchievementId(string $achievementName): ?int
    {
        $achievement = Achievement::where('name', $achievementName)
            ->select('id')
            ->first();

        if ($achievement) {
            return $achievement->id;
        }

        return null;
    }

    public function setAchievement(string $achievementName, int $user_id): void
    {
        $achievementId = $this->getAchievementId($achievementName);

        if ($achievementId) {
            try {
                DB::table('achievement_user')
                    ->insert([
                        'achievement_id' => $achievementId,
                        'user_id' => $user_id
                    ]);
            } catch (Exception $exception) {
                Log::alert($exception->getMessage(), [
                    'achievement_id' => $achievementId,
                    'user_id' => $user_id
                ]);
            }
        }
    }

    public function hasAchievement(string $achievementName, int $user_id): ?bool
    {
        $achievementId = $this->getAchievementId($achievementName);

        if ($achievementId) {
            return DB::table('achievement_user')
                ->where('achievement_id', $achievementId)
                ->where('user_id', $user_id)
                ->exists();
        }

        return null;
    }

    public function nextAchievementFor(): array
    {
        $unlockedAchievements = $this->achievements->pluck('name');

        $nextAchievement = Achievement::whereNotIn('name', $unlockedAchievements)
            ->select('name')
            ->orderBy('id')
            ->get();

        return $nextAchievement ? $nextAchievement->pluck('name')->toArray() : [];
    }

    public function currentBadgeName(): ?string
    {
        return $this->badge ? $this->badge->name : null;
    }

    public function unlockBadge(): string
    {
        $unlockedAchievementsCount = $this->achievements()->count();
        $badgeRequirements = Badge::orderBy('requirement', 'desc')
            ->pluck('requirement', 'name');

        foreach ($badgeRequirements as $badgeName => $requirement) {
            if ($unlockedAchievementsCount >= $requirement) {
                return $badgeName;
            }
        }

        return 'Beginner';
    }

    public function getBadgeId(string $badgeName): ?int
    {
        $badge = Badge::where('name', $badgeName)
            ->select('id')
            ->first();

        if ($badge) {
            return $badge->id;
        }

        return null;
    }
    public function setBadge(): void
    {
        $badgeName = $this->unlockBadge();

        if ($this->currentBadgeName() === $badgeName) {
            return;
        }

        $badgeId = $this->getBadgeId($badgeName);

        if ($badgeId)
        {
            $this->update([
                 'badge_id' => $badgeId
            ]);
        }
    }

    public function nextBadgeFor(): ?string
    {
        $unlockedAchievementsCount = $this->achievements()->count();
        $badgeRequirements = Badge::pluck('requirement', 'name');

        foreach ($badgeRequirements as $badgeName => $requirement) {
            if ($unlockedAchievementsCount < $requirement) {
                return $badgeName;
            }
        }

        return null;
    }

    public function remainingToUnlockNextBadge(): int
    {
        $unlockedAchievementsCount = $this->achievements()->count();
        $badgeRequirements = Badge::pluck('requirement', 'name');

        foreach ($badgeRequirements as $requirement) {
            if ($unlockedAchievementsCount < $requirement) {
                return $requirement - $unlockedAchievementsCount;
            }
        }

        return 0;
    }
}

