<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Information;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Middleware cek admin dipasang di rute api.php

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

    // Daftar informasi yang belum diverifikasi (Sudah diperbaiki dengan relasi)
    public function pendingInformations()
    {
        // Berhasil memanggil data informasi beserta objek user pembuatnya
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