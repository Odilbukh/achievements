<?php

namespace App\Models;

use App\Enums\BadgeRequirementsEnum;
use App\Enums\BadgesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'requirement'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
