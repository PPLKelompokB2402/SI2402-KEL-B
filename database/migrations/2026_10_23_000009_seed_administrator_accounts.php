<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $passwordHash = Hash::make('12345678');

        DB::transaction(function () use ($passwordHash): void {
            $superAdminId = DB::table('users')->insertGetId([
                'nama' => 'Super Admin Utama',
                'email' => 'superadmin@kolabora.test',
                'password_hash' => $passwordHash,
                'no_hp' => null,
                'role' => 'super_admin',
                'status_aktif' => true,
                'created_at' => now(),
            ], 'id_user');

            DB::table('super_admin')->insert([
                'id_super_admin' => $superAdminId,
                'hak_akses' => 'penuh',
            ]);

            $adminId = DB::table('users')->insertGetId([
                'nama' => 'Admin Utama',
                'email' => 'admin@kolabora.test',
                'password_hash' => $passwordHash,
                'no_hp' => null,
                'role' => 'admin',
                'status_aktif' => true,
                'created_at' => now(),
            ], 'id_user');

            DB::table('administrator')->insert([
                'id_admin' => $adminId,
                'level_admin' => 'operasional',
                'id_super_admin' => $superAdminId,
            ]);
        });
    }

    public function down(): void
    {
        $adminIds = DB::table('users')
            ->whereIn('email', ['admin@kolabora.test', 'superadmin@kolabora.test'])
            ->pluck('id_user');

        DB::table('administrator')->whereIn('id_admin', $adminIds)->delete();
        DB::table('super_admin')->whereIn('id_super_admin', $adminIds)->delete();
        DB::table('users')->whereIn('id_user', $adminIds)->delete();
    }
};
