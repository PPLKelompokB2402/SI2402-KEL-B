<?php

namespace Tests\Feature;

use App\Models\KategoriLayanan;
use App\Models\User;
use App\Models\Vendor;
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

    protected function buatVendor(array $attributes = []): Vendor
    {
        $user = User::factory()->role('vendor')->create();

        return Vendor::create(array_merge([
            'id_user' => $user->id_user,
            'id_kategori' => KategoriLayanan::first()->id_kategori,
            'nama_usaha' => 'Vendor '.$user->id_user,
            'deskripsi_usaha' => 'Deskripsi layanan vendor.',
            'lokasi_layanan' => 'Jakarta',
            'kisaran_harga' => 5000000,
            'status_verifikasi' => Vendor::TERVERIFIKASI,
        ], $attributes))->load('user');
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
