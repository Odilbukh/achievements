<?php

namespace App\Http\Controllers;

use App\Events\LessonWatched;
use App\Http\Requests\LessonWatchedRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LessonWatchedController extends Controller
{
    public function __invoke(LessonWatchedRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $isWatched = DB::table('lesson_user')
            ->where('lesson_id', $validated['lesson_id'])
            ->where('user_id', $validated['user_id'])
            ->where('watched', true)
            ->exists();

        if ($isWatched)
        {
            return response()->json([
                'message' => 'The lesson was watched already',
            ]);
        }

        $newRecord = DB::table('lesson_user')
            ->insert([
                'lesson_id' => $validated['lesson_id'],
                'user_id' => $validated['user_id'],
                'watched' => true,
            ]);

        event(new LessonWatched($validated['user_id']));

        return response()->json([
            'message' => 'The lesson watched successfully',
            'data' => $newRecord
        ], 201);
    }
}
