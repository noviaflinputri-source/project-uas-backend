<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['disabilitas', 'relawan', 'admin'])->default('disabilitas');
            $table->timestamp('verified_at')->nullable(); // null = belum diverifikasi admin
        });
    }
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'verified_at']);
        });
    }
};