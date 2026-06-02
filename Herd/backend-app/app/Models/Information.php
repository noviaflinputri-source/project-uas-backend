<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    // Deklarasikan nama tabel secara tegas
    protected $table = 'informations';

    // WAJIB: Daftarkan kolom agar tidak terkena Mass Assignment Error 500
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_verified',
        'verified_by'
    ];
}