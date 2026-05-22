<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    // Hanya penyandang disabilitas yang bisa membuat cerita
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'disabilitas') {
            return response()->json(['message' => 'Hanya penyandang disabilitas yang dapat berbagi cerita'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $story = Story::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content
        ]);

        return response()->json($story, 201);
    }

    // Semua role bisa melihat cerita (dengan informasi penulis)
    public function index()
    {
        $stories = Story::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($stories);
    }

    // Detail cerita termasuk komentar
    public function show($id)
    {
        $story = Story::with(['user', 'comments.user'])->findOrFail($id);
        return response()->json($story);
    }
}