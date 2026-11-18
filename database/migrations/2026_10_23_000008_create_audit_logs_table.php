<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('id_audit');
            $table->foreignId('id_admin')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->string('aksi', 100);
            $table->string('entitas', 100);
            $table->unsignedBigInteger('id_entitas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
