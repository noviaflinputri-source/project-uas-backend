<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // 1. DISESUAIKAN: Nama fungsi diubah dari fetchMessages menjadi getMessages agar cocok dengan route kamu
    public function getMessages($help_request_id)
    {
        $chats = Chat::where('help_request_id', $help_request_id)
                     ->orderBy('created_at', 'asc')
                     ->get();

        return response()->json([
            'success' => true,
            'data'    => $chats
        ], 200);
    }

    // 2. TETAP: Sesuai dengan route Route::post('/chats')
    public function sendMessage(Request $request)
    {
        $request->validate([
            'help_request_id' => 'required|integer',
            'sender_id'       => 'required|integer',
            'receiver_id'     => 'required|integer',
            'message'         => 'required|string',
        ]);

        // Simpan pesan ke database HeidiSQL
        $chat = Chat::create([
            'help_request_id' => $request->help_request_id,
            'sender_id'       => $request->sender_id,
            'receiver_id'     => $request->receiver_id,
            'message'         => $request->message,
            'is_read'         => 0,
        ]);

        // Siarkan sinyal real-time lewat Reverb
        broadcast(new MessageSent($chat))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim!',
            'data'    => $chat
        ], 201);
    }
}