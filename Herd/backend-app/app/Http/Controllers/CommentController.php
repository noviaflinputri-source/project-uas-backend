<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Relawan (atau admin) bisa komentar
    public function store(Request $request, $story_id)
    {
        $user = $request->user();
        if (!in_array($user->role, ['relawan', 'admin'])) {
            return response()->json(['message' => 'Hanya relawan yang dapat mengomentari cerita'], 403);
        }

        $story = Story::findOrFail($story_id);

        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = Comment::create([
            'story_id' => $story->id,
            'user_id' => $user->id,
            'comment' => $request->comment
        ]);

        return response()->json($comment, 201);
    }

    // Ambil komentar dari suatu cerita
    public function index($story_id)
    {
        $story = Story::findOrFail($story_id);
        $comments = $story->comments()->with('user')->get();
        return response()->json($comments);
    }
}