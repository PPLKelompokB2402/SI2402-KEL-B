<?php

namespace Tests\Feature;

use App\Models\DokumenLegalitas;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * PBI-02 — vendor mengunggah dokumen legalitas (KTP, NIB/SIUP) untuk verifikasi.
 */
class Us02LegalitasTest extends KolaboraTestCase
{
    public function test_vendor_mengunggah_dokumen_legalitas_secara_privat(): void
    {
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $user = $vendor->user;

        $this->actingAs($user, 'web')->post(route('vendor.legalitas.unggah', $vendor), [
            'jenis_dokumen' => 'KTP', 'no_dokumen' => '3201xxxx', 'berkas' => UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf'),
        ])->assertSessionHasNoErrors()->assertRedirect('/vendor/profil?vendor='.$vendor->id_vendor);

        $this->actingAs($user, 'web')->get('/vendor/profil?vendor='.$vendor->id_vendor)->assertOk()
            ->assertSee('KTP Penanggung Jawab')->assertSee('Menunggu pemeriksaan');
        $dokumen = DokumenLegalitas::firstOrFail();
        Storage::disk('local')->assertExists($dokumen->media_url);

        // Pemilik dapat membuka berkasnya, publik tidak.
        $this->actingAs($user, 'web')->get(route('media.dokumen', $dokumen))->assertOk();
        auth('web')->logout();
        $this->get(route('media.dokumen', $dokumen))->assertForbidden();
        $lain = User::factory()->role('vendor')->create();
        $this->actingAs($lain, 'web')->get(route('media.dokumen', $dokumen))->assertForbidden();
    }

    public function test_unggah_ulang_mengganti_dokumen_lama(): void
    {
        $vendor = $this->buatVendor();
        foreach (['ktp-lama.pdf', 'ktp-baru.pdf'] as $nama) {
            $this->actingAs($vendor->user, 'web')->post(route('vendor.legalitas.unggah', $vendor), [
                'jenis_dokumen' => 'KTP', 'no_dokumen' => '3201', 'berkas' => UploadedFile::fake()->create($nama, 50, 'application/pdf'),
            ])->assertSessionHasNoErrors();
        }

        $this->assertSame(1, DokumenLegalitas::count());
        $this->assertCount(1, Storage::disk('local')->allFiles('legalitas'));
        $this->assertSame(Vendor::MENUNGGU, $vendor->fresh()->status_verifikasi);
    }

    public function test_format_dan_ukuran_berkas_divalidasi(): void
    {
        $vendor = $this->buatVendor();

        $this->actingAs($vendor->user, 'web')->post(route('vendor.legalitas.unggah', $vendor), [
            'jenis_dokumen' => 'KTP', 'no_dokumen' => '1', 'berkas' => UploadedFile::fake()->create('ktp.exe', 10),
        ])->assertSessionHasErrors('berkas');
        $this->actingAs($vendor->user, 'web')->post(route('vendor.legalitas.unggah', $vendor), [
            'jenis_dokumen' => 'NIB', 'no_dokumen' => '1', 'berkas' => UploadedFile::fake()->create('nib.pdf', 5000, 'application/pdf'),
        ])->assertSessionHasErrors('berkas');
        $this->assertSame(0, DokumenLegalitas::count());
    }
}
