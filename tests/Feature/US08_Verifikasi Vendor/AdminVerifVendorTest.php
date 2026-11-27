<?php

namespace Tests\Feature\US08_VerifikasiVendor;

use App\Models\DokumenLegalitas;
use App\Models\Vendor;
use Tests\Feature\KolaboraTestCase;

class AdminVerifVendorTest extends KolaboraTestCase
{
    public function test_tc01_verifikasi_vendor_dengan_dokumen_lengkap(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $ktp = $this->buatDokumen($vendor, 'KTP');
        $nib = $this->buatDokumen($vendor, 'NIB');

        foreach ([$ktp, $nib] as $dokumen) {
            $this->sebagaiAdmin($admin)
                ->patch(route('admin.dokumen.periksa', $dokumen), [
                    'status_verifikasi' => DokumenLegalitas::VALID,
                ])
                ->assertRedirect();
        }

        $this->sebagaiAdmin($admin)
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::TERVERIFIKASI])
            ->assertRedirect(route('admin.verifikasi'));

        $this->assertSame(Vendor::TERVERIFIKASI, $vendor->fresh()->status_verifikasi);
        foreach ([$ktp, $nib] as $dokumen) {
            $this->assertSame(DokumenLegalitas::VALID, $dokumen->fresh()->status_verifikasi);
            $this->assertSame($admin->id_user, $dokumen->fresh()->id_admin);
        }
    }

    public function test_tc02_tombol_verifikasi_nonaktif_jika_nib_belum_diunggah(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatDokumen($vendor, 'KTP');

        $response = $this->sebagaiAdmin($admin)->get(route('admin.verifikasi'));

        $response->assertOk()
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Dokumen KTP dan NIB/SIUP belum diunggah atau ada yang ditolak');
    }

    public function test_tc03_tombol_verifikasi_nonaktif_jika_nib_ditolak(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatDokumen($vendor, 'KTP');
        $this->buatDokumen($vendor, 'NIB', DokumenLegalitas::DITOLAK);

        $response = $this->sebagaiAdmin($admin)->get(route('admin.verifikasi'));

        $response->assertOk()
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Dokumen KTP dan NIB/SIUP belum diunggah atau ada yang ditolak');
    }

    public function test_tc04_permintaan_verifikasi_ditolak_jika_dokumen_belum_lengkap(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatDokumen($vendor, 'KTP');

        $this->sebagaiAdmin($admin)
            ->from(route('admin.verifikasi'))
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::TERVERIFIKASI])
            ->assertSessionHasErrors('status_verifikasi');

        $this->assertSame(Vendor::MENUNGGU, $vendor->fresh()->status_verifikasi);
    }

    public function test_tc05_admin_dapat_menolak_vendor(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor([
            'status_verifikasi' => Vendor::MENUNGGU,
        ]);
        $this->buatDokumen($vendor, 'KTP');
        $this->buatDokumen($vendor, 'NIB');

        $this->sebagaiAdmin($admin)
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::DITOLAK])
            ->assertRedirect(route('admin.verifikasi'));

        $this->assertSame(Vendor::DITOLAK, $vendor->fresh()->status_verifikasi);
    }

    private function buatDokumen(Vendor $vendor, string $jenis, string $status = DokumenLegalitas::MENUNGGU): DokumenLegalitas
    {
        return $vendor->dokumenLegalitas()->create([
            'jenis_dokumen' => $jenis,
            'no_dokumen' => 'DOK-'.$jenis,
            'media_url' => 'legalitas/'.$jenis.'.pdf',
            'status_verifikasi' => $status,
        ]);
    }
}
