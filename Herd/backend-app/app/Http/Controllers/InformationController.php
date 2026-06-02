<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InformationController extends Controller
{
    // 1. MENGAMBIL SEMUA DATA INFORMASI UNTUK REACT
    public function index()
    {
        $informasi = Information::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data'    => $informasi
        ], 200);
    }

    // 2. MENYIMPAN DATA INFORMASI BARU KE DATABASE
    public function store(Request $request)
    {
        // Validasi input dari frontend
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Ambil ID pengguna dari token Sanctum, jika tidak ada pakai input request, fallback ke 1
        $userId = auth()->id() ?? $request->input('user_id') ?? 1;

        // Simpan ke tabel 'informations'
        $informasi = Information::create([
            'user_id'     => $userId,
            'title'       => $request->input('title'),
            'content'     => $request->input('content'),
            'is_verified' => false, 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Informasi berhasil disimpan ke database!',
            'data'    => $informasi
        ], 201);
    }
}