<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    // Menghubungkan ke nama tabel asli kamu di phpMyAdmin
    protected $table = 'chats'; 

    // PERBAIKAN UTAMA: Menggunakan guarded kosong berarti membiarkan semua kolom 
    // menerima data murni (termasuk NULL) secara langsung tanpa hambatan validasi model!
    protected $guarded = [];
}