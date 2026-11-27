<?php

namespace Tests\Feature;

use App\Models\Vendor;

/**
 * PBI-08 — administrator memperbarui status verifikasi vendor.
 */
class Us08StatusVerifikasiTest extends KolaboraTestCase
{
    public function test_vendor_hanya_dapat_diverifikasi_jika_dokumen_lengkap(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);

        $this->sebagaiAdmin($admin)->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => 'terverifikasi'])->assertSessionHasErrors('status_verifikasi');

        $ktp = $vendor->dokumenLegalitas()->create(['jenis_dokumen' => 'KTP', 'no_dokumen' => '1', 'media_url' => 'legalitas/a.pdf']);
        $nib = $vendor->dokumenLegalitas()->create(['jenis_dokumen' => 'NIB', 'no_dokumen' => '2', 'media_url' => 'legalitas/b.pdf', 'status_verifikasi' => 'ditolak', 'catatan_admin' => 'Buram']);
        $this->sebagaiAdmin($admin)->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => 'terverifikasi'])->assertSessionHasErrors('status_verifikasi');

        // Dokumen lengkap tetapi belum ditandai Valid: masih ditolak dan vendor tetap di antrean.
        $nib->update(['status_verifikasi' => 'menunggu', 'catatan_admin' => null]);
        $this->sebagaiAdmin($admin)->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => 'terverifikasi'])
            ->assertSessionHasErrors(['status_verifikasi' => 'Dokumen KTP dan NIB/SIUP belum ditandai Valid. Periksa dokumen di halaman detail vendor terlebih dahulu.']);
        $this->assertSame(Vendor::MENUNGGU, $vendor->fresh()->status_verifikasi);

        // Setelah KTP dan NIB ditandai Valid, vendor dapat diverifikasi.
        foreach ([$ktp, $nib] as $dokumen) {
            $this->sebagaiAdmin($admin)->patch(route('admin.dokumen.periksa', $dokumen), ['status_verifikasi' => 'valid']);
        }
        $this->sebagaiAdmin($admin)->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => 'terverifikasi'])
            ->assertRedirect(route('admin.verifikasi'));
        $this->assertSame(Vendor::TERVERIFIKASI, $vendor->fresh()->status_verifikasi);
        $this->assertSame($admin->id_user, $nib->fresh()->id_admin);

        // Vendor yang terverifikasi langsung tampil di katalog.
        auth('web')->logout();
        $this->get('/')->assertSee($vendor->nama_usaha);
    }

    public function test_admin_menolak_vendor(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);

        $this->sebagaiAdmin($admin)->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => 'ditolak'])->assertRedirect();
        $this->assertSame(Vendor::DITOLAK, $vendor->fresh()->status_verifikasi);
        $this->actingAs($vendor->user, 'web')->get('/vendor/usaha')->assertSee('Ditolak');
    }
}
