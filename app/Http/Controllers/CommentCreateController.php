<?php

namespace App\Http\Controllers;

use App\Events\CommentWritten;
use App\Http\Requests\CreateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class CommentCreateController extends Controller
{
    public function __invoke(CreateCommentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $comment = Comment::create([
            'body' => $validated['text'],
            'user_id' => $validated['user_id'],
        ]);

        event(new CommentWritten($validated['user_id']));

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ], 201);
    }
}
