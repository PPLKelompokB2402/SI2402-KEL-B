<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Dasar pengujian fitur KolaboraEO: database bersih, storage palsu, dan pembantu pembuatan akun.
 */
abstract class KolaboraTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        config()->set('auth.super_admin', ['name' => null, 'email' => null, 'password' => null]);
        $this->seed(DatabaseSeeder::class);
    }

    /** Masuk sebagai administrator memakai guard yang berlaku pada tahap ini. */
    protected function sebagaiAdmin(User $admin): static
    {
        return $this->actingAs($admin, 'web');
    }

    /** Masuk sebagai super admin memakai guard yang berlaku pada tahap ini. */
    protected function sebagaiSuperAdmin(User $superAdmin): static
    {
        return $this->actingAs($superAdmin, 'web');
    }

    protected function buatPelanggan(): User
    {
        $user = User::factory()->create();
        $user->pelanggan()->create(['alamat' => 'Jakarta']);

        return $user;
    }

    protected function buatAdmin(?string $email = null): User
    {
        $user = User::factory()->role('admin')->create($email ? ['email' => $email] : []);
        $user->administrator()->create(['level_admin' => 'operasional']);

        return $user;
    }

    protected function buatSuperAdmin(): User
    {
        $user = User::factory()->role('super_admin')->create();
        $user->superAdmin()->create(['hak_akses' => 'penuh']);

        return $user;
    }
}
