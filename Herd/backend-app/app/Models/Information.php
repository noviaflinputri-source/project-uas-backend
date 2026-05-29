<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika Laravel tidak otomatis mendeteksinya sebagai jamak
    protected $table = 'informations';

    // Daftarkan kolom yang boleh diisi (sesuaikan dengan kolom di database kamu)
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_verified',
        'verified_by',
    ];

    /**
     * Relasi ke model User (Pembuat Informasi)
     * Solusi nomor 2 yang kamu pilih
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}