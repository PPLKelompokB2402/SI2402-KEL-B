<?php

namespace Tests\Feature;

use App\Models\User;

/**
 * Akun Super Admin pertama dibuat lewat seeder/perintah artisan.
 */
class Us00PerintahAkunTest extends KolaboraTestCase
{
    public function test_command_membuat_super_admin_pertama(): void
    {
        $this->artisan('admin:create-super-admin', ['nama' => 'Boot Admin', 'email' => 'boot@example.test'])
            ->expectsQuestion('Kata sandi (minimal 8 karakter)', 'rahasia123')
            ->assertExitCode(0);

        $user = User::where('email', 'boot@example.test')->firstOrFail();
        $this->assertDatabaseHas('super_admin', ['id_super_admin' => $user->id_user]);
        $this->post('/login', ['email' => 'boot@example.test', 'password' => 'rahasia123'])->assertRedirect('/super-admin/dashboard');
        $this->get('/super-admin/dashboard')->assertOk();
    }

    public function test_seeder_membuat_super_admin_dari_konfigurasi(): void
    {
        config()->set('auth.super_admin', ['name' => 'Seed Admin', 'email' => 'seed@example.test', 'password' => 'rahasia123']);
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'seed@example.test', 'role' => 'super_admin']);
    }

    public function test_command_membuat_administrator_sebelum_portal_super_admin_tersedia(): void
    {
        $super = $this->buatSuperAdmin();

        $this->artisan('admin:create-admin', ['nama' => 'Admin Satu', 'email' => 'satu@example.test'])
            ->expectsQuestion('Kata sandi (minimal 8 karakter)', 'rahasia123')
            ->assertExitCode(0);

        $admin = User::where('email', 'satu@example.test')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertDatabaseHas('administrator', ['id_admin' => $admin->id_user, 'id_super_admin' => $super->id_user]);
    }
}
