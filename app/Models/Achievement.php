<?php

namespace App\Models;

use App\Enums\AchievementsEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    protected $fillable = [
        'name'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
