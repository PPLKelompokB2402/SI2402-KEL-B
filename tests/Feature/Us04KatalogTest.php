<?php

namespace Tests\Feature;

use App\Models\Vendor;

/**
 * PBI-04 — katalog hanya menampilkan vendor terverifikasi.
 */
class Us04KatalogTest extends KolaboraTestCase
{
    public function test_katalog_hanya_menampilkan_vendor_terverifikasi(): void
    {
        $this->buatVendor(['nama_usaha' => 'Senja Dekor']);
        $this->buatVendor(['nama_usaha' => 'Rasa Katering']);
        $this->buatVendor(['nama_usaha' => 'Belum Verif', 'status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatVendor(['nama_usaha' => 'Sudah Ditolak', 'status_verifikasi' => Vendor::DITOLAK]);

        $this->get('/')->assertOk()
            ->assertSee('Senja Dekor')->assertSee('Rasa Katering')->assertSee('Profil terverifikasi')
            ->assertDontSee('Belum Verif')->assertDontSee('Sudah Ditolak');
    }

    public function test_katalog_kosong_dan_paginasi(): void
    {
        $this->get('/')->assertOk()->assertSee('Belum ada vendor');

        foreach (range(1, 7) as $i) {
            $this->buatVendor(['nama_usaha' => sprintf('Vendor Urut %02d', $i)]);
        }
        $this->get('/')->assertSee('Vendor Urut 01')->assertDontSee('Vendor Urut 07');
        $this->get('/?page=2')->assertSee('Vendor Urut 07');
    }
}
