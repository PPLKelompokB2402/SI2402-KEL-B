<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas Vendor (ERD no. 3) — profil usaha. Satu akun User (role vendor)
 * dapat memiliki lebih dari satu profil usaha (id_user, relasi 1:N), dan
 * relasi "Mengelompokkan" N:1 dengan KategoriLayanan (kategori utama).
 *
 * saldo_escrow dan no_rekening_bank tidak dibuat karena fitur pembayaran
 * escrow tidak termasuk PBI pada jadwal pengembangan. badge_usaha juga tidak
 * dibuat karena seluruh vendor KolaboraEO adalah UMKM.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->id('id_vendor');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('id_kategori')->constrained('kategori_layanan', 'id_kategori')->restrictOnDelete();
            $table->string('nama_usaha', 150);
            $table->text('deskripsi_usaha');
            $table->string('lokasi_layanan', 150)->index();
            $table->decimal('kisaran_harga', 15, 2);
            $table->string('status_verifikasi', 20)->default('menunggu')->index(); // menunggu | terverifikasi | ditolak
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
