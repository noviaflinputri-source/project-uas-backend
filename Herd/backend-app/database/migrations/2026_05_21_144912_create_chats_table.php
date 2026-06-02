<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            
            // help_request_id tidak dicentang Allow NULL di gambar, jadi biarkan begini
            $table->foreignId('help_request_id')->constrained()->onDelete('cascade');
            
            // 🛠️ TAMBAHKAN ->nullable() agar Allow NULL dicentang dan default-nya NULL
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->text('message');
            
            // 🛠️ TAMBAHKAN ->nullable() juga di sini agar Allow NULL-nya tercentang seperti di gambar
            $table->tinyInteger('is_read')->nullable()->default(0); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};