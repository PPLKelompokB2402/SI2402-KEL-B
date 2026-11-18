<?php

namespace Tests\Feature;

use App\Models\Vendor;

/**
 * PBI-06 — halaman detail vendor (ringkasan, portofolio, harga, kontak).
 */
class Us06DetailVendorTest extends KolaboraTestCase
{
    public function test_detail_vendor_terverifikasi_dapat_dibuka(): void
    {
        $vendor = $this->buatVendor(['nama_usaha' => 'Senja Dekor', 'deskripsi_usaha' => 'Dekorasi pernikahan adat.']);
        $vendor->portofolio()->create(['judul_proyek' => 'Wedding Garden', 'media_url' => 'a.jpg', 'media_watermark_url' => 'b.jpg']);

        $this->get('/')->assertSee(route('vendors.show', $vendor));
        $this->get(route('vendors.show', $vendor))->assertOk()
            ->assertSee('Senja Dekor')->assertSee('Dekorasi pernikahan adat.')->assertSee('wa.me/62');
        $this->get(route('vendors.portofolio', $vendor))->assertOk()->assertSee('Wedding Garden');
        $this->get(route('vendors.harga', $vendor))->assertOk()->assertSee('Rp5.000.000');
    }

    public function test_detail_vendor_belum_terverifikasi_tidak_ditemukan(): void
    {
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);

        $this->get(route('vendors.show', $vendor))->assertNotFound();
        $this->get(route('vendors.portofolio', $vendor))->assertNotFound();
    }
}
