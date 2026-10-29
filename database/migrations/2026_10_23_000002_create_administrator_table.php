<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas Administrator (ERD no. 4) — relasi "Sebagai" 1:1 dengan User
 * dan relasi "Mengelola" N:1 dengan SuperAdmin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrator', function (Blueprint $table) {
            $table->foreignId('id_admin')->primary()->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('level_admin', 50)->default('operasional');
            $table->foreignId('id_super_admin')->nullable()->constrained('super_admin', 'id_super_admin')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrator');
    }
};
