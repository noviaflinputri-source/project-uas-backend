<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // 1. Mengambil data riwayat chat dari database
    public function getMessages($help_request_id)
    {
        $chats = Chat::where('help_request_id', $help_request_id)
                     ->orderBy('created_at', 'asc')
                     ->get();

        return response()->json($chats, 200);
    }

    // 2. Menghubungkan rute default POST /api/chats ke fungsi kirim pesan (Store)
    public function store(Request $request)
    {
        return $this->sendMessage($request);
    }

    // 3. Fungsi Kirim Pesan Langsung Tembus Database
    public function sendMessage(Request $request)
    {
        // Langsung instansiasi objek murni untuk bypass database constraint
        $chat = new Chat();
        $chat->help_request_id = $request->has('help_request_id') ? (int)$request->help_request_id : 1;
        $chat->sender_id       = $request->has('sender_id') ? (int)$request->sender_id : null;
        $chat->receiver_id     = $request->has('receiver_id') ? (int)$request->receiver_id : null;
        $chat->message         = (string)$request->message;
        $chat->is_read         = 0;
        
        // Simpan data pesan ke database MySQL yang sudah kamu ubah jadi 'Null: Yes'
        $chat->save(); 

        // Mengembalikan response sukses murni berbentuk JSON ke React
        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim!',
            'data'    => $chat
        ], 201);
    }
}
