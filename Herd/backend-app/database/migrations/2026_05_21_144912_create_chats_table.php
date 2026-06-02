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
            
            // Kolom utama relasi bantuan
            $table->unsignedBigInteger('help_request_id');
            
            // Kolom relasi user (Dibuat nullable agar kotak "Allow NULL" tercentang hijau)
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            
            $table->text('message');
            $table->tinyInteger('is_read')->nullable()->default(0); 
            $table->timestamps();

            // 🛠️ PENGATURAN FOREIGN KEY (DISESUAIKAN DENGAN GAMBAR KAMU)
            // 1. help_request_id menggunakan CASCADE saat didelete
            $table->foreign('help_request_id')
                  ->references('id')
                  ->on('help_requests')
                  ->onDelete('cascade');

            // 2. sender_id menggunakan NO ACTION saat didelete
            $table->foreign('sender_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action');

            // 3. receiver_id menggunakan NO ACTION saat didelete
            $table->foreign('receiver_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action');
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