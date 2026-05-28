<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'description', 'status', 'relawan_id', 'category'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function relawan()
    {
        return $this->belongsTo(User::class, 'relawan_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}