<?php

namespace Tests\Feature;

use App\Models\KategoriLayanan;

/**
 * PBI-05 — pencarian dan filter katalog (kata kunci, kategori, lokasi, harga).
 */
class Us05PencarianFilterTest extends KolaboraTestCase
{
    public function test_pencarian_dan_filter_katalog(): void
    {
        $dekor = KategoriLayanan::where('nama_kategori', 'Dekorasi')->first();
        $katering = KategoriLayanan::where('nama_kategori', 'Katering')->first();
        $this->buatVendor(['nama_usaha' => 'Senja Dekor', 'id_kategori' => $dekor->id_kategori, 'lokasi_layanan' => 'Bandung', 'kisaran_harga' => 5000000]);
        $this->buatVendor(['nama_usaha' => 'Rasa Katering', 'id_kategori' => $katering->id_kategori, 'lokasi_layanan' => 'Jakarta', 'kisaran_harga' => 15000000]);

        $this->get('/?q=senja')->assertOk()->assertSee('Senja Dekor')->assertDontSee('Rasa Katering');
        $this->get('/?q=katering')->assertSee('Rasa Katering')->assertDontSee('Senja Dekor');
        $this->get('/?lokasi=jakarta')->assertSee('Rasa Katering')->assertDontSee('Senja Dekor');
        $this->get('/?kategori='.$dekor->id_kategori)->assertSee('Senja Dekor')->assertDontSee('Rasa Katering');
        $this->get('/?harga_min=10000000&harga_max=20000000')->assertSee('Rasa Katering')->assertDontSee('Senja Dekor');
        $this->get('/?q=tidakada')->assertSee('Belum ada vendor yang cocok');
    }

    public function test_urutan_harga_dan_validasi_rentang(): void
    {
        $this->buatVendor(['nama_usaha' => 'Mahal Sekali', 'kisaran_harga' => 90000000]);
        $this->buatVendor(['nama_usaha' => 'Murah Meriah', 'kisaran_harga' => 1000000]);

        $this->get('/?urut=harga')->assertSeeInOrder(['Murah Meriah', 'Mahal Sekali']);
        $this->get('/?harga_min=5000&harga_max=1000')->assertSessionHasErrors('harga_max');
    }
}
