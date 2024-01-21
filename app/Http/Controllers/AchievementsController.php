<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user): JsonResponse
    {
        return response()->json([
            'unlocked_achievements' => $user->achievements()->pluck('name')->toArray(),
            'next_available_achievements' => $user->nextAchievementFor(),
            'current_badge' => '',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }
}
