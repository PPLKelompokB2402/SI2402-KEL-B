<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $credentials = config('auth.super_admin');

        if (array_filter($credentials, fn ($value) => filled($value)) === []) {
            $this->command?->warn('Super Admin tidak dibuat. Isi SUPER_ADMIN_NAME, SUPER_ADMIN_EMAIL, dan SUPER_ADMIN_PASSWORD di .env.');

            return;
        }

        if (User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])->exists()) {
            $this->command?->info('Akun administrator sudah tersedia; Super Admin dari seeder dilewati.');

            return;
        }

        $validator = Validator::make($credentials, [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw new RuntimeException('Konfigurasi Super Admin tidak valid: '.$validator->errors()->first());
        }

        DB::transaction(function () use ($credentials): void {
            $user = User::create([
                'nama' => $credentials['name'],
                'email' => $credentials['email'],
                'password_hash' => $credentials['password'],
                'role' => User::ROLE_SUPER_ADMIN,
                'status_aktif' => true,
            ]);
            $user->superAdmin()->create(['hak_akses' => 'penuh']);
        });

        $this->command?->info('Akun Super Admin berhasil dibuat dari konfigurasi .env.');
    }
}
