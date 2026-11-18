<?php

namespace Tests\Feature;

use App\Models\Vendor;

/**
 * PBI-07 — administrator meninjau dokumen legalitas vendor.
 */
class Us07TinjauDokumenTest extends KolaboraTestCase
{
    public function test_admin_melihat_antrean_dan_memeriksa_dokumen(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatVendor(['nama_usaha' => 'Sudah Terverifikasi']);

        $this->sebagaiAdmin($admin)->get('/admin/verifikasi')->assertOk()
            ->assertSee($vendor->nama_usaha)->assertSee('Dokumen belum lengkap')->assertDontSee('Sudah Terverifikasi');

        $vendor->dokumenLegalitas()->create(['jenis_dokumen' => 'KTP', 'no_dokumen' => '1', 'media_url' => 'legalitas/a.pdf']);
        $nib = $vendor->dokumenLegalitas()->create(['jenis_dokumen' => 'NIB', 'no_dokumen' => '2', 'media_url' => 'legalitas/b.pdf']);
        $this->sebagaiAdmin($admin)->get('/admin/verifikasi')->assertSee('Dokumen lengkap');
        $this->sebagaiAdmin($admin)->get(route('admin.verifikasi.detail', $vendor))->assertOk()->assertSee('Buka berkas');

        // Penolakan dokumen wajib disertai catatan yang tampil bagi vendor.
        $this->sebagaiAdmin($admin)->patch(route('admin.dokumen.periksa', $nib), ['status_verifikasi' => 'ditolak'])->assertSessionHasErrors('catatan_admin');
        $this->sebagaiAdmin($admin)->patch(route('admin.dokumen.periksa', $nib), ['status_verifikasi' => 'ditolak', 'catatan_admin' => 'Buram'])->assertSessionHasNoErrors();
        $this->assertSame('ditolak', $nib->fresh()->status_verifikasi);
        $this->assertSame($admin->id_user, $nib->fresh()->id_admin);

        $this->actingAs($vendor->user, 'web')->get('/vendor/profil')->assertSee('Catatan admin: Buram');
    }

    public function test_hanya_admin_yang_dapat_meninjau(): void
    {
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $dokumen = $vendor->dokumenLegalitas()->create(['jenis_dokumen' => 'KTP', 'no_dokumen' => '1', 'media_url' => 'legalitas/a.pdf']);

        $this->actingAs($vendor->user, 'web')->patch(route('admin.dokumen.periksa', $dokumen), ['status_verifikasi' => 'valid'])
            ->assertRedirect('/admin/login');
        $this->assertSame('menunggu', $dokumen->fresh()->status_verifikasi);
    }
}
