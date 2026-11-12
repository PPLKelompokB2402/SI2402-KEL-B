<?php

namespace Tests\Feature;

use App\Models\Portofolio;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * PBI-03 — vendor mengunggah portofolio dengan watermark otomatis.
 */
class Us03PortofolioTest extends KolaboraTestCase
{
    public function test_portofolio_disimpan_dengan_versi_watermark_dan_dapat_dihapus(): void
    {
        $vendor = $this->buatVendor();
        $user = $vendor->user;

        $this->actingAs($user, 'web')->get('/vendor/portofolio')->assertOk()->assertSee($vendor->nama_usaha);
        $this->actingAs($user, 'web')->post(route('vendor.portofolio.simpan', $vendor), [
            'judul_proyek' => 'Wedding Garden', 'media' => UploadedFile::fake()->image('foto.jpg', 800, 600),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $portofolio = Portofolio::firstOrFail();
        Storage::disk('local')->assertExists([$portofolio->media_url, $portofolio->media_watermark_url]);
        $this->assertNotSame($portofolio->media_url, $portofolio->media_watermark_url);

        // Versi ber-watermark dapat diakses publik.
        auth('web')->logout();
        $this->get(route('media.portofolio', $portofolio))->assertOk();

        $this->actingAs($user, 'web')->delete(route('vendor.portofolio.hapus', $portofolio))->assertRedirect();
        $this->assertModelMissing($portofolio);
        Storage::disk('local')->assertMissing([$portofolio->media_url, $portofolio->media_watermark_url]);
    }

    public function test_hanya_gambar_yang_diterima_dan_hanya_pemilik_yang_menghapus(): void
    {
        $vendor = $this->buatVendor();
        $this->actingAs($vendor->user, 'web')->post(route('vendor.portofolio.simpan', $vendor), [
            'judul_proyek' => 'Dokumen', 'media' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('media');

        $portofolio = $vendor->portofolio()->create(['judul_proyek' => 'X', 'media_url' => 'a.jpg', 'media_watermark_url' => 'b.jpg']);
        $lain = User::factory()->role('vendor')->create();
        $this->actingAs($lain, 'web')->delete(route('vendor.portofolio.hapus', $portofolio))->assertForbidden();
        $this->actingAs($lain, 'web')->post(route('vendor.portofolio.simpan', $vendor), ['judul_proyek' => 'X'])->assertForbidden();
    }
}
