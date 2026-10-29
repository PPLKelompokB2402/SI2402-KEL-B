<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas Pelanggan (ERD no. 2) — relasi "Sebagai" 1:1 dengan User.
 * Nama dan email tidak disimpan ulang karena sudah ada di tabel users.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->foreignId('id_pelanggan')->primary()->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('alamat')->nullable();
            $table->string('foto_profile')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
