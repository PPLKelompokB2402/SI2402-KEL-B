<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas DokumenLegalitas (ERD no. 7) — relasi "Memiliki" dengan Vendor dan
 * relasi "Memverifikasi" N:1 dengan Administrator.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_legalitas', function (Blueprint $table) {
            $table->id('id_legalitas');
            $table->foreignId('id_vendor')->constrained('vendor', 'id_vendor')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()->constrained('administrator', 'id_admin')->nullOnDelete();
            $table->string('jenis_dokumen', 20); // KTP | NIB | SIUP
            $table->string('no_dokumen', 50);
            $table->string('media_url');
            $table->string('status_verifikasi', 20)->default('menunggu'); // menunggu | valid | ditolak
            $table->string('catatan_admin', 500)->nullable();
            $table->unique(['id_vendor', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_legalitas');
    }
};
