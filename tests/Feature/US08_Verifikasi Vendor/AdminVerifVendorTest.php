<?php

namespace Tests\Feature\US08_VerifikasiVendor;

use App\Models\DokumenLegalitas;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\KolaboraTestCase;

class AdminVerifVendorTest extends KolaboraTestCase
{
    public function test_tc01_verifikasi_vendor_dengan_dokumen_lengkap(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $ktp = $this->buatDokumen($vendor, 'KTP');
        $nib = $this->buatDokumen($vendor, 'NIB');
        $menunggu = $this->buatVendor([
            'nama_usaha' => 'Vendor Menunggu',
            'status_verifikasi' => Vendor::MENUNGGU,
        ]);
        $ditolak = $this->buatVendor([
            'nama_usaha' => 'Vendor Ditolak',
            'status_verifikasi' => Vendor::DITOLAK,
        ]);

        $this->sebagaiAdmin($admin)
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::TERVERIFIKASI])
            ->assertRedirect(route('admin.verifikasi'));

        $this->assertSame(Vendor::TERVERIFIKASI, $vendor->fresh()->status_verifikasi);
        foreach ([$ktp, $nib] as $dokumen) {
            $this->assertSame(DokumenLegalitas::VALID, $dokumen->fresh()->status_verifikasi);
            $this->assertSame($admin->id_user, $dokumen->fresh()->id_admin);
        }

        auth('web')->logout();
        $this->get(route('vendors.index'))
            ->assertOk()
            ->assertSee($vendor->nama_usaha)
            ->assertDontSee($menunggu->nama_usaha)
            ->assertDontSee($ditolak->nama_usaha);
    }

    public function test_tc02_tombol_verifikasi_nonaktif_jika_nib_belum_diunggah(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatDokumen($vendor, 'KTP');

        $response = $this->sebagaiAdmin($admin)->get(route('admin.verifikasi'));

        $response->assertOk()->assertSeeHtml(
            '<button class="button button-dark" type="submit" disabled title="Dokumen KTP dan NIB/SIUP belum lengkap">Verifikasi</button>'
        );
    }

    public function test_tc03_tombol_verifikasi_nonaktif_jika_nib_ditolak(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatDokumen($vendor, 'KTP');
        $this->buatDokumen($vendor, 'NIB', DokumenLegalitas::DITOLAK);

        $response = $this->sebagaiAdmin($admin)->get(route('admin.verifikasi'));

        $response->assertOk()->assertSeeHtml(
            '<button class="button button-dark" type="submit" disabled title="Dokumen KTP dan NIB/SIUP belum lengkap">Verifikasi</button>'
        );
    }

    public function test_tc04_permintaan_verifikasi_ditolak_jika_dokumen_belum_lengkap(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);

        $this->sebagaiAdmin($admin)
            ->from(route('admin.verifikasi'))
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::TERVERIFIKASI])
            ->assertSessionHasErrors('status_verifikasi');

        $this->assertSame(Vendor::MENUNGGU, $vendor->fresh()->status_verifikasi);
    }

    public function test_tc05_admin_dapat_menolak_vendor_dan_vendor_tidak_muncul_di_katalog(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor([
            'nama_usaha' => 'Vendor Ditolak dari Katalog',
            'status_verifikasi' => Vendor::MENUNGGU,
        ]);
        $vendorTerverifikasi = $this->buatVendor([
            'nama_usaha' => 'Vendor Tetap di Katalog',
            'status_verifikasi' => Vendor::TERVERIFIKASI,
        ]);

        $this->sebagaiAdmin($admin)
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::DITOLAK])
            ->assertRedirect(route('admin.verifikasi'));

        $this->assertSame(Vendor::DITOLAK, $vendor->fresh()->status_verifikasi);
        auth('web')->logout();
        $this->get(route('vendors.index'))
            ->assertOk()
            ->assertSee($vendorTerverifikasi->nama_usaha)
            ->assertDontSee(route('vendors.show', $vendor), false);
    }

    public function test_tc06_vendor_dilarang_membuka_halaman_admin(): void
    {
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);

        $this->actingAs($vendor->user, 'web')
            ->get(route('admin.verifikasi'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_tc07_perubahan_status_mencatat_audit_log(): void
    {
        $admin = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $this->buatDokumen($vendor, 'KTP');
        $this->buatDokumen($vendor, 'NIB');

        $this->sebagaiAdmin($admin)
            ->patch(route('admin.vendors.status', $vendor), ['status_verifikasi' => Vendor::TERVERIFIKASI])
            ->assertRedirect(route('admin.verifikasi'));
        $this->assertSame(Vendor::TERVERIFIKASI, $vendor->fresh()->status_verifikasi);

        $auditLogTercatat = Schema::hasTable('audit_logs')
            && DB::table('audit_logs')->exists();
        $this->assertTrue($auditLogTercatat, 'Audit log belum tersedia atau belum mencatat perubahan status.');
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
