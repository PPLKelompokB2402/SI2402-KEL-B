<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Sementara (sebelum PBI-12): akun administrator dibuat lewat perintah ini.
 * Mulai PBI-12 akun administrator dikelola super admin dari portalnya.
 */
class CreateAdmin extends Command
{
    protected $signature = 'admin:create-admin {nama} {email}';

    protected $description = 'Membuat akun administrator (dikelola oleh Super Admin pertama)';

    public function handle(): int
    {
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->orderBy('id_user')->first();

        if (! $superAdmin) {
            $this->error('Buat akun Super Admin terlebih dahulu (php artisan admin:create-super-admin atau db:seed).');

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

        DB::transaction(function () use ($password, $superAdmin): void {
            $user = User::create([
                'nama' => $this->argument('nama'),
                'email' => $this->argument('email'),
                'password_hash' => $password,
                'role' => User::ROLE_ADMIN,
                'status_aktif' => true,
            ]);
            $user->administrator()->create(['id_super_admin' => $superAdmin->id_user]);
        });

        $this->info('Akun administrator berhasil dibuat.');

        return self::SUCCESS;
    }
}
