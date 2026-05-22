<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    // Relawan membuat info (perlu verifikasi admin)
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'relawan') {
            return response()->json(['message' => 'Hanya relawan yang dapat berbagi informasi'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $info = Information::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'is_verified' => false
        ]);

        return response()->json(['message' => 'Informasi berhasil dikirim, menunggu verifikasi admin', 'info' => $info], 201);
    }

    // Tampilkan informasi: untuk disabilitas hanya yang verified; relawan lihat semua milik sendiri + verified
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'disabilitas') {
            $infos = Information::where('is_verified', true)->with('user')->get();
        } else {
            // relawan: info milik sendiri (belum verified) + info yang sudah verified (milik siapa saja)
            $infos = Information::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('is_verified', true);
            })->with('user')->get();
        }
        return response()->json($infos);
    }

    // Relawan dapat mengedit info miliknya sendiri (selama belum diverifikasi)
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $info = Information::findOrFail($id);

        if ($info->user_id !== $user->id || $user->role !== 'relawan') {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        if ($info->is_verified) {
            return response()->json(['message' => 'Informasi yang sudah diverifikasi tidak bisa diubah'], 400);
        }

        $request->validate([
            'title' => 'string|max:255',
            'content' => 'string'
        ]);

        $info->update($request->only(['title', 'content']));
        return response()->json($info);
    }

    // Hapus info (hanya oleh pembuat atau admin)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $info = Information::findOrFail($id);

        if ($info->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $info->delete();
        return response()->json(['message' => 'Informasi dihapus']);
    }
}