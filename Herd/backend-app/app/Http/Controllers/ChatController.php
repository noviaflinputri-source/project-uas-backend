<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\HelpRequest;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Kirim pesan
    public function sendMessage(Request $request)
    {
        $request->validate([
            'help_request_id' => 'required|exists:help_requests,id',
            'message' => 'required|string'
        ]);

        $user = $request->user();
        $help = HelpRequest::findOrFail($request->help_request_id);

        // Pastikan user terlibat dalam bantuan ini
        if ($user->id !== $help->user_id && $user->id !== $help->relawan_id) {
            return response()->json(['message' => 'Anda tidak terlibat dalam percakapan ini'], 403);
        }

        $receiverId = ($user->id === $help->user_id) ? $help->relawan_id : $help->user_id;

        $chat = Chat::create([
            'help_request_id' => $help->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message
        ]);

        return response()->json($chat, 201);
    }

    // Ambil semua chat dari suatu help request
    public function getMessages(Request $request, $help_request_id)
    {
        $user = $request->user();
        $help = HelpRequest::findOrFail($help_request_id);

        if ($user->id !== $help->user_id && $user->id !== $help->relawan_id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $chats = Chat::where('help_request_id', $help_request_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai pesan yang diterima sebagai sudah dibaca
        Chat::where('help_request_id', $help_request_id)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        return response()->json($chats);
    }
}