<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Information;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ==========================================
    // FUNGSI BARU: STATISTIK DASHBOARD REAL DATA
    // ==========================================
    public function getDashboardStats()
    {
        try {
            // 1. Hitung total data asli dari database
            $totalPengguna = User::count();
            $totalInformasi = Information::count();
            
            // Menggunakan backslash (\) sebagai jalan pintas panggil model help_requests tanpa perlu import di atas
            $totalBantuan = \App\Models\HelpRequest::count(); 

            // 2. Ambil 5 pengguna terbaru yang mendaftar untuk tabel dashboard
            $penggunaTerbaru = User::orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'name', 'email', 'status']);

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_users' => $totalPengguna,
                    'total_helps' => $totalBantuan,
                    'total_informations' => $totalInformasi,
                ],
                'latest_users' => $penggunaTerbaru
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // FUNGSI BAWAAN: VERIFIKASI USER & INFORMASI
    // ==========================================

    // Daftar user pending
    public function pendingUsers()
    {
        $users = User::where('status', 'pending')->get();
        return response()->json($users);
    }

    // Verifikasi user (approve/reject)
    public function verifyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => "User status diubah menjadi {$request->status}"]);
    }

    // Daftar informasi yang belum diverifikasi
    public function pendingInformations()
    {
        $infos = Information::where('is_verified', false)->with('user')->get();
        return response()->json($infos);
    }

    // Verifikasi informasi
    public function verifyInformation(Request $request, $id)
    {
        $info = Information::findOrFail($id);
        $admin = $request->user();

        $request->validate([
            'is_verified' => 'required|boolean'
        ]);

        $info->is_verified = $request->is_verified;
        $info->verified_by = $admin->id;
        $info->save();

        return response()->json(['message' => 'Status verifikasi diperbarui', 'info' => $info]);
    }
}