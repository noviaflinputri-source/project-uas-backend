<?php

namespace App\Http\Controllers;

use App\Models\HelpRequest;
use App\Models\User;
use Illuminate\Http\Request;

class HelpRequestController extends Controller
{
    // Hanya penyandang disabilitas: buat permintaan bantuan
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            // Fallback ID jika testing tanpa token, gunakan default ID 5 sesuai phpMyAdmin
            $userId = $user ? $user->id : 5;

            // Proses validasi field data dari request react
            $request->validate([
                'description' => 'required|string',
                'category'    => 'required|string' 
            ]);

            // Menyimpan data ke database
            $help = HelpRequest::create([
                'user_id'     => $userId,
                'description' => $request->description,
                'category'    => $request->category, 
                'status'      => 'pending'
            ]);

            return response()->json($help, 201)
                ->header('Access-Control-Allow-Origin', '*');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Proses validasi data gagal.',
                'errors' => $e->errors()
            ], 422)->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kendala pada sistem database internal: ' . $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    // List permintaan bantuan (DITAMBAHKAN HEADER CORS BIAR TEMBUS KE REACT PORT 9000)
    public function index(Request $request)
    {
        // Ambil semua data bantuan yang berstatus pending langsung dari tabel help_requests
        $helps = HelpRequest::where('status', 'pending')->latest()->get();

        // Mapping data user secara aman agar tidak memicu error kosong jika relasi retak
        $customHelps = $helps->map(function($item) {
            $findUser = User::find($item->user_id);
            
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'description' => $item->description,
                'category' => $item->category,
                'status' => $item->status,
                'relawan_id' => $item->relawan_id,
                'user' => [
                    'name' => $findUser ? $findUser->name : 'Pemohon Bantuan #' . $item->user_id,
                    'email' => $findUser ? $findUser->email : 'tidakadaemail@gmail.com'
                ]
            ];
        });
        
        // Mengembalikan response dengan paksaan Header CORS agar lolos sensor browser
        return response()->json($customHelps)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    // Relawan menerima permintaan bantuan (Tombol ACC dengan Header CORS)
    public function accept(Request $request, $id)
    {
        try {
            $user = $request->user();
            $relawanId = $user ? $user->id : 1;

            $help = HelpRequest::findOrFail($id);
            if ($help->status !== 'pending') {
                return response()->json(['message' => 'Permintaan sudah tidak tersedia atau sudah di-ACC relawan lain'], 400)
                                 ->header('Access-Control-Allow-Origin', '*');
            }

            // Update status di database menjadi accepted
            $help->update([
                'relawan_id' => $relawanId,
                'status' => 'accepted'
            ]);

            return response()->json([
                'message' => 'Permintaan diterima, silakan buka chat', 
                'help' => $help
            ], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyetujui bantuan: ' . $e->getMessage()], 500)
                             ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // Detail help request (SUDAH DIPERBAIKi UNTUK KEBUTUHAN POLLING NOTIFIKASI REACT)
    public function show($id)
    {
        $help = HelpRequest::findOrFail($id);
        $findUser = User::find($help->user_id);
        $findRelawan = User::find($help->relawan_id);

        return response()->json([
            'id' => $help->id,
            'user_id' => $help->user_id,
            'relawan_id' => $help->relawan_id, // PERBAIKAN UTAMA: Dikirim ke React agar polling if(relawan_id) bisa mendeteksi nilai true
            'description' => $help->description,
            'category' => $help->category,      // Menampilkan kategori bantuan agar sinkron di komponen chat privat
            'status' => $help->status,
            'user' => $findUser,
            'relawan' => $findRelawan
        ])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
}