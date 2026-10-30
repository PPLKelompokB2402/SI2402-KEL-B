<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas KategoriLayanan (ERD no. 6) — 3 atribut.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_layanan', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 80)->unique();
            $table->string('deskripsi', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_layanan');
    }
};
