<?php

namespace Tests\Feature;

use App\Models\KategoriLayanan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Schema;

/**
 * PBI-01 — vendor membuat dan mengelola profil usaha (satu akun dapat memiliki beberapa usaha).
 */
class Us01ProfilUsahaTest extends KolaboraTestCase
{
    public function test_tabel_kategori_dan_vendor_sesuai_erd(): void
    {
        $this->assertSame(['id_kategori', 'nama_kategori', 'deskripsi'], Schema::getColumnListing('kategori_layanan'));
        $this->assertEqualsCanonicalizing(
            ['id_vendor', 'id_user', 'id_kategori', 'nama_usaha', 'deskripsi_usaha', 'lokasi_layanan', 'kisaran_harga', 'status_verifikasi'],
            Schema::getColumnListing('vendor'),
        );
        $this->assertFalse(Schema::hasColumn('vendor', 'saldo_escrow'));
        $this->assertFalse(Schema::hasColumn('vendor', 'badge_usaha'));
        $this->assertDatabaseCount('kategori_layanan', 6);
    }

    public function test_vendor_membuat_beberapa_profil_usaha_dan_mengubahnya(): void
    {
        $user = User::factory()->role('vendor')->create();
        $kategori = KategoriLayanan::first();

        $this->actingAs($user, 'web')->get('/vendor/dashboard')->assertOk()->assertSee('Tambahkan usaha');
        $this->actingAs($user, 'web')->get('/vendor/profil')->assertRedirect('/vendor/usaha');

        foreach (['Senja Dekor', 'Senja Katering'] as $nama) {
            $this->actingAs($user, 'web')->post('/vendor/usaha', [
                'nama_usaha' => $nama, 'id_kategori' => $kategori->id_kategori, 'deskripsi_usaha' => 'Layanan acara pernikahan.',
                'lokasi_layanan' => 'Bandung', 'kisaran_harga' => 7500000, 'no_hp' => '081111111111',
            ])->assertSessionHasNoErrors()->assertRedirect();
        }
        $this->assertSame(2, $user->vendors()->count());
        $this->actingAs($user, 'web')->get('/vendor/usaha')->assertOk()->assertSee('Senja Dekor')->assertSee('Senja Katering');

        $vendor = $user->vendors()->where('nama_usaha', 'Senja Dekor')->firstOrFail();
        $this->assertSame(Vendor::MENUNGGU, $vendor->status_verifikasi);
        $this->actingAs($user, 'web')->get('/vendor/profil?vendor='.$vendor->id_vendor)->assertOk()->assertSee('Senja Dekor');

        $this->actingAs($user, 'web')->put(route('vendor.usaha.update', $vendor), [
            'nama_usaha' => 'Senja Dekorasi', 'id_kategori' => $kategori->id_kategori, 'deskripsi_usaha' => 'Dekorasi pernikahan.',
            'lokasi_layanan' => 'Bandung', 'kisaran_harga' => 8000000, 'no_hp' => '081111111111',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Senja Dekorasi', $vendor->fresh()->nama_usaha);
    }

    public function test_profil_usaha_divalidasi_dan_hanya_dapat_diubah_pemiliknya(): void
    {
        $vendor = $this->buatVendor();

        $this->actingAs($vendor->user, 'web')->post('/vendor/usaha', ['nama_usaha' => ''])
            ->assertSessionHasErrors(['nama_usaha', 'id_kategori', 'deskripsi_usaha', 'lokasi_layanan', 'kisaran_harga', 'no_hp']);

        $lain = User::factory()->role('vendor')->create();
        $this->actingAs($lain, 'web')->put(route('vendor.usaha.update', $vendor), ['nama_usaha' => 'X'])->assertForbidden();
    }

    public function test_perubahan_profil_terverifikasi_diajukan_ulang(): void
    {
        $vendor = $this->buatVendor();

        $this->actingAs($vendor->user, 'web')->put(route('vendor.usaha.update', $vendor), [
            'nama_usaha' => 'Nama Baru', 'id_kategori' => $vendor->id_kategori, 'deskripsi_usaha' => 'Deskripsi baru.',
            'lokasi_layanan' => 'Bogor', 'kisaran_harga' => 1000000, 'no_hp' => '081111111111',
        ])->assertRedirect();
        $this->assertSame(Vendor::MENUNGGU, $vendor->fresh()->status_verifikasi);
    }
}
