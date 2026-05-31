<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats'; 

    protected $fillable = [
        'help_request_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read'
    ];
}