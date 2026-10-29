<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas User (ERD no. 1) — 8 atribut.
 * Tabel sessions adalah tabel bawaan Laravel untuk menyimpan sesi login.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama', 120);
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('no_hp', 20)->nullable();
            $table->string('role', 20)->index(); // pelanggan | vendor | admin | super_admin
            $table->boolean('status_aktif')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
