<?php

namespace App\Http\Controllers;

use App\Models\HelpRequest;
use Illuminate\Http\Request;

class HelpRequestController extends Controller
{
    // Hanya penyandang disabilitas: buat permintaan bantuan
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'disabilitas') {
            return response()->json(['message' => 'Hanya penyandang disabilitas yang dapat meminta bantuan'], 403);
        }

        // Validasi ditambah agar category wajib diisi dan berupa string
        $request->validate([
            'description' => 'required|string',
            'category'    => 'required|string' 
        ]);

        // Menyimpan data ke database termasuk kolom category baru
        $help = HelpRequest::create([
            'user_id'     => $user->id,
            'description' => $request->description,
            'category'    => $request->category, // <-- Kolom baru kamu nangkring di sini!
            'status'      => 'pending'
        ]);

        return response()->json($help, 201);
    }

    // List permintaan bantuan (untuk relawan: semua pending; untuk disabilitas: miliknya sendiri)
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'relawan') {
            $helps = HelpRequest::where('status', 'pending')->with('user')->get();
        } else {
            $helps = HelpRequest::where('user_id', $user->id)->get();
        }
        return response()->json($helps);
    }

    // Relawan menerima permintaan bantuan
    public function accept(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'relawan') {
            return response()->json(['message' => 'Hanya relawan yang dapat menerima bantuan'], 403);
        }

        $help = HelpRequest::findOrFail($id);
        if ($help->status !== 'pending') {
            return response()->json(['message' => 'Permintaan sudah tidak tersedia'], 400);
        }

        $help->update([
            'relawan_id' => $user->id,
            'status' => 'accepted'
        ]);

        return response()->json(['message' => 'Permintaan diterima, silakan buka chat', 'help' => $help]);
    }

    // Detail help request (untuk chat)
    public function show($id)
    {
        $help = HelpRequest::with(['user', 'relawan'])->findOrFail($id);
        return response()->json($help);
    }
}