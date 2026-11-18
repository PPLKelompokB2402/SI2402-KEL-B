<?php

namespace Tests\Feature;

use App\Models\KategoriLayanan;

/**
 * PBI-09 — mengelola kategori layanan.
 */
class Us09KategoriTest extends KolaboraTestCase
{
    public function test_admin_dan_super_admin_mengelola_kategori(): void
    {
        $admin = $this->buatAdmin();
        $this->sebagaiAdmin($admin)->get('/admin/kategori')->assertOk()->assertSee('Dekorasi');
        $this->sebagaiAdmin($admin)->post('/admin/kategori', ['nama_kategori' => 'Sound System', 'deskripsi' => 'Audio'])->assertRedirect();
        $kategori = KategoriLayanan::where('nama_kategori', 'Sound System')->firstOrFail();
        $this->sebagaiAdmin($admin)->patch(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Sound & Lighting'])->assertRedirect();
        $this->assertSame('Sound & Lighting', $kategori->fresh()->nama_kategori);
        $this->sebagaiAdmin($admin)->post('/admin/kategori', ['nama_kategori' => 'Dekorasi'])->assertSessionHasErrors('nama_kategori');

        auth('web')->logout();
        $super = $this->buatSuperAdmin();
        $this->sebagaiSuperAdmin($super)->get('/super-admin/kategori')->assertOk()->assertSee('Sound &amp; Lighting', false);
        $this->sebagaiSuperAdmin($super)->post('/super-admin/kategori', ['nama_kategori' => 'Venue Outdoor'])->assertRedirect();
        $this->assertDatabaseHas('kategori_layanan', ['nama_kategori' => 'Venue Outdoor']);
    }

    public function test_kategori_baru_dapat_dipilih_vendor(): void
    {
        KategoriLayanan::create(['nama_kategori' => 'Henna Art']);
        $user = \App\Models\User::factory()->role('vendor')->create();

        $this->actingAs($user, 'web')->get('/vendor/usaha')->assertSee('Henna Art');
    }
}
