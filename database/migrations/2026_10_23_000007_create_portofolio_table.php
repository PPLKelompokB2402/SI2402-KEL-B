<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas Portofolio (ERD no. 8) — relasi "Mengunggah" N:1 dengan Vendor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portofolio', function (Blueprint $table) {
            $table->id('id_portofolio');
            $table->foreignId('id_vendor')->constrained('vendor', 'id_vendor')->cascadeOnDelete();
            $table->string('judul_proyek', 120);
            $table->string('deskripsi_proyek', 500)->nullable();
            $table->string('media_url');
            $table->string('media_watermark_url');
            $table->timestamp('tanggal_unggah')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portofolio');
    }
};
