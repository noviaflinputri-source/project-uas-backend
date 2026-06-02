<?php

namespace App\Http\Controllers; // Pastikan murni folder Controllers utama

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    // 1. Mengambil semua data informasi
    public function index()
    {
        try {
            $informasi = Information::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data'    => $informasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2. Menyimpan data informasi baru ke database
    public function store(Request $request)
    {
        // Ambil data user_id dari React, jika kosong default ke angka 1
        $userId = $request->input('user_id') ?? 1;

        try {
            // Simpan data murni menggunakan Eloquent Model
            $informasi = Information::create([
                'user_id'     => $userId,
                'title'       => $request->input('title') ?? 'Judul Default',
                'content'     => $request->input('content') ?? 'Isi Default',
                'is_verified' => 0, // default false sesuai database HeidiSQL
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Informasi berhasil disimpan ke database!',
                'data'    => $informasi
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}