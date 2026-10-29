<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas SuperAdmin (ERD no. 5) — relasi "Sebagai" 1:1 dengan User.
 * id_super_admin sekaligus primary key dan foreign key ke users.id_user.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admin', function (Blueprint $table) {
            $table->foreignId('id_super_admin')->primary()->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('hak_akses', 50)->default('penuh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin');
    }
};
