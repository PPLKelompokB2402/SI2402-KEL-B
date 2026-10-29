<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'admin:create-super-admin {nama} {email}';

    protected $description = 'Membuat akun Super Admin pertama jika belum ada administrator';

    public function handle(): int
    {
        if (User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])->exists()) {
            $this->error('Administrator sudah tersedia. Gunakan portal super admin untuk mengelola akun.');

            return self::FAILURE;
        }

        $password = $this->secret('Kata sandi (minimal 8 karakter)');
        $validator = Validator::make([
            'nama' => $this->argument('nama'),
            'email' => $this->argument('email'),
            'password' => $password,
        ], [
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        DB::transaction(function () use ($password): void {
            $user = User::create([
                'nama' => $this->argument('nama'),
                'email' => $this->argument('email'),
                'password_hash' => $password,
                'role' => User::ROLE_SUPER_ADMIN,
                'status_aktif' => true,
            ]);
            $user->superAdmin()->create(['hak_akses' => 'penuh']);
        });

        $this->info('Akun Super Admin berhasil dibuat.');

        return self::SUCCESS;
    }
}
