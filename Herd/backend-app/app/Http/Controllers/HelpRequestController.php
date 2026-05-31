<?php

namespace App\Http\Controllers;

use App\Models\HelpRequest;
use Illuminate\Http\Request;

class HelpRequestController extends Controller
{
    // Hanya penyandang disabilitas: buat permintaan bantuan
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['message' => 'Sesi login tidak valid, silakan login ulang.'], 401);
            }

            // TETEP NYALA: Validasi fungsionalitas role demi penilaian UAS yang objektif
            if ($user->role !== 'disabilitas') {
                return response()->json([
                    'message' => 'Hanya pengguna dengan akun "disabilitas" yang diizinkan mengirim formulir ini. Akun Anda saat ini terdaftar sebagai role: ' . $user->role
                ], 403);
            }

            // Proses validasi field data dari request react
            $request->validate([
                'description' => 'required|string',
                'category'    => 'required|string' 
            ]);

            // Menyimpan data ke database relasi table
            $help = HelpRequest::create([
                'user_id'     => $user->id,
                'description' => $request->description,
                'category'    => $request->category, 
                'status'      => 'pending'
            ]);

            return response()->json($help, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Proses validasi data gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kendala pada sistem database internal: ' . $e->getMessage()
            ], 500);
        }
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