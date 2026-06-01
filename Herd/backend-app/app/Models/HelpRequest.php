<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    use HasFactory;

    // Menentukan nama tabel jika diperlukan (opsional, karena Laravel otomatis mendeteksi jamak dari nama model)
    protected $table = 'help_requests';

    // Field yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'user_id', 
        'description', 
        'category',
        'status', 
        'relawan_id'
    ];

    /**
     * Relasi ke model User (Penyandang Disabilitas yang membuat permintaan)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model User (Relawan yang menyetujui/ACC bantuan)
     */
    public function relawan()
    {
        return $this->belongsTo(User::class, 'relawan_id');
    }

    /**
     * Relasi ke model Chat (Pesan obrolan yang terikat dengan bantuan ini)
     */
    public function chats()
    {
        return $this->hasMany(Chat::class, 'help_request_id');
    }
}