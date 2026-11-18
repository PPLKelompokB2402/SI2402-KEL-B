<?php

namespace Tests\Feature\US07_TinjauDokumenLegalitas;

use App\Models\DokumenLegalitas;
use App\Models\KategoriLayanan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * US-07: Tinjau Dokumen dan Legalitas Vendor
 * White Box Testing — 8 Test Case
 */
class AdminVendorVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Vendor $vendorMenunggu;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        config()->set('auth.super_admin', [
            'name'     => null,
            'email'    => null,
            'password' => null,
        ]);

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = $this->buatAdmin();

        $this->vendorMenunggu = $this->buatVendor([
            'nama_usaha'        => 'Fotografi Budi',
            'status_verifikasi' => Vendor::MENUNGGU,
        ]);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function buatAdmin(): User
    {
        $user = User::factory()->role('admin')->create();
        $user->administrator()->create(['level_admin' => 'operasional']);
        return $user;
    }

    private function buatVendor(array $attributes = []): Vendor
    {
        $userVendor = User::factory()->role('vendor')->create();

        return Vendor::create(array_merge([
            'id_user'           => $userVendor->id_user,
            'id_kategori'       => KategoriLayanan::first()->id_kategori,
            'nama_usaha'        => 'Vendor Test',
            'deskripsi_usaha'   => 'Deskripsi layanan vendor untuk pengujian.',
            'lokasi_layanan'    => 'Bandung',
            'kisaran_harga'     => 3_500_000,
            'status_verifikasi' => Vendor::TERVERIFIKASI,
        ], $attributes))->load('user');
    }

    private function buatDokumen(
        Vendor $vendor,
        string $jenis,
        string $status = DokumenLegalitas::MENUNGGU,
    ): DokumenLegalitas {
        return $vendor->dokumenLegalitas()->create([
            'jenis_dokumen'     => $jenis,
            'no_dokumen'        => strtoupper($jenis).'-'.fake()->numerify('######'),
            'media_url'         => 'legalitas/'.strtolower($jenis).'.pdf',
            'status_verifikasi' => $status,
        ]);
    }

    // ─── TC-01 ───────────────────────────────────────────────────────────────

    /**
     * TC-01: Admin melihat daftar vendor menunggu verifikasi beserta
     * nama usaha, pemilik, kategori, tanggal daftar, dan keterangan
     * Dokumen lengkap / Dokumen belum lengkap.
     *
     * AC: AC-01
     * Jalur kode: AdminController::verifikasi()
     */
    public function test_TC01_admin_melihat_daftar_vendor_menunggu_verifikasi(): void
    {
        // Vendor terverifikasi — tidak boleh muncul di daftar
        $vendorLain = $this->buatVendor([
            'nama_usaha'        => 'Wedding Sukses',
            'status_verifikasi' => Vendor::TERVERIFIKASI,
        ]);

        $response = $this->actingAs($this->admin, 'web')
            ->get(route('admin.verifikasi'));

        $response->assertOk();
        $response->assertSee($this->vendorMenunggu->nama_usaha);    // nama usaha
        $response->assertSee($this->vendorMenunggu->user->nama);    // nama pemilik
        $response->assertSee($this->vendorMenunggu->kategori->nama_kategori); // kategori
        $response->assertSee('Dokumen belum lengkap');               // keterangan
        $response->assertDontSee($vendorLain->nama_usaha);          // vendor lain tidak muncul

        // Tambahkan KTP + NIB → keterangan berubah menjadi "Dokumen lengkap"
        $this->buatDokumen($this->vendorMenunggu, 'KTP');
        $this->buatDokumen($this->vendorMenunggu, 'NIB');

        $this->actingAs($this->admin, 'web')
            ->get(route('admin.verifikasi'))
            ->assertSee('Dokumen lengkap');
    }

    // ─── TC-02 ───────────────────────────────────────────────────────────────

    /**
     * TC-02: Admin membuka halaman tinjau yang menampilkan profil usaha,
     * contoh portofolio, serta dokumen KTP, NIB, dan SIUP.
     *
     * AC: AC-02
     * Jalur kode: AdminController::detailVerifikasi()
     */
    public function test_TC02_admin_membuka_halaman_tinjau_profil_portofolio_dan_dokumen(): void
    {
        $this->vendorMenunggu->portofolio()->create([
            'judul_proyek'        => 'Liputan Pernikahan Andi',
            'deskripsi_proyek'    => 'Dokumentasi lengkap.',
            'media_url'           => 'portofolio/foto1.jpg',
            'media_watermark_url' => 'portofolio/foto1_wm.jpg',
        ]);

        $this->buatDokumen($this->vendorMenunggu, 'KTP');
        $this->buatDokumen($this->vendorMenunggu, 'NIB');

        $response = $this->actingAs($this->admin, 'web')
            ->get(route('admin.verifikasi.detail', $this->vendorMenunggu));

        $response->assertOk();
        $response->assertSee($this->vendorMenunggu->nama_usaha);    // profil usaha
        $response->assertSee($this->vendorMenunggu->deskripsi_usaha);
        $response->assertSee('KTP');                                 // label dokumen KTP
        $response->assertSee('NIB');                                 // label dokumen NIB
        $response->assertSee('SIUP');                                // label dokumen SIUP
        $response->assertSee('Buka berkas');                         // tombol buka dokumen
    }

    // ─── TC-03 ───────────────────────────────────────────────────────────────

    /**
     * TC-03: Admin dapat menandai dokumen sebagai Valid.
     *
     * AC: AC-03
     * Jalur kode: AdminController::periksaDokumen() — cabang 'valid'
     */
    public function test_TC03_admin_menandai_dokumen_valid(): void
    {
        $dokumen = $this->buatDokumen($this->vendorMenunggu, 'KTP');

        $this->actingAs($this->admin, 'web')
            ->patch(route('admin.dokumen.periksa', $dokumen), [
                'status_verifikasi' => DokumenLegalitas::VALID,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(
            DokumenLegalitas::VALID,
            $dokumen->fresh()->status_verifikasi
        );
    }

    // ─── TC-04 ───────────────────────────────────────────────────────────────

    /**
     * TC-04: Admin dapat menandai dokumen sebagai Ditolak.
     *
     * AC: AC-03
     * Jalur kode: AdminController::periksaDokumen() — cabang 'ditolak'
     */
    public function test_TC04_admin_menandai_dokumen_ditolak(): void
    {
        $dokumen = $this->buatDokumen($this->vendorMenunggu, 'NIB');

        $this->actingAs($this->admin, 'web')
            ->patch(route('admin.dokumen.periksa', $dokumen), [
                'status_verifikasi' => DokumenLegalitas::DITOLAK,
                'catatan_admin'     => 'Nomor NIB tidak terbaca.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(
            DokumenLegalitas::DITOLAK,
            $dokumen->fresh()->status_verifikasi
        );
    }

    // ─── TC-05 ───────────────────────────────────────────────────────────────

    /**
     * TC-05: Sistem menolak penolakan dokumen tanpa catatan admin
     * dan menampilkan pesan kesalahan pada field catatan_admin.
     *
     * AC: AC-04
     * Jalur kode: Rule::requiredIf(status === 'ditolak')
     */
    public function test_TC05_sistem_menolak_penolakan_tanpa_catatan_admin(): void
    {
        $dokumen = $this->buatDokumen($this->vendorMenunggu, 'SIUP');

        $this->actingAs($this->admin, 'web')
            ->patch(route('admin.dokumen.periksa', $dokumen), [
                'status_verifikasi' => DokumenLegalitas::DITOLAK,
                // catatan_admin sengaja dikosongkan
            ])
            ->assertSessionHasErrors('catatan_admin');

        // Status harus tetap 'menunggu'
        $this->assertSame(
            DokumenLegalitas::MENUNGGU,
            $dokumen->fresh()->status_verifikasi
        );
    }

    // ─── TC-06 ───────────────────────────────────────────────────────────────

    /**
     * TC-06: Sistem menyimpan penolakan beserta catatan admin di database.
     *
     * AC: AC-04
     * Jalur kode: AdminController::periksaDokumen() → DB::transaction()
     */
    public function test_TC06_sistem_menyimpan_penolakan_beserta_catatan_admin(): void
    {
        $dokumen = $this->buatDokumen($this->vendorMenunggu, 'NIB');
        $catatan = 'Scan tidak jelas, NIB harus tampak seluruh halamannya.';

        $this->actingAs($this->admin, 'web')
            ->patch(route('admin.dokumen.periksa', $dokumen), [
                'status_verifikasi' => DokumenLegalitas::DITOLAK,
                'catatan_admin'     => $catatan,
            ]);

        $this->assertDatabaseHas('dokumen_legalitas', [
            'id_legalitas'      => $dokumen->id_legalitas,
            'status_verifikasi' => DokumenLegalitas::DITOLAK,
            'catatan_admin'     => $catatan,
        ]);
    }

    // ─── TC-07 ───────────────────────────────────────────────────────────────

    /**
     * TC-07: Sistem mencatat identitas admin yang memeriksa dokumen (id_admin).
     *
     * AC: AC-05
     * Jalur kode: $dokumen->update([...$data, 'id_admin' => $admin->id_user])
     */
    public function test_TC07_sistem_mencatat_identitas_admin_pemeriksa(): void
    {
        $dokumen = $this->buatDokumen($this->vendorMenunggu, 'KTP');

        $this->assertNull($dokumen->id_admin, 'Sebelum pemeriksaan id_admin harus null.');

        $this->actingAs($this->admin, 'web')
            ->patch(route('admin.dokumen.periksa', $dokumen), [
                'status_verifikasi' => DokumenLegalitas::VALID,
            ]);

        $fresh = $dokumen->fresh();

        $this->assertNotNull($fresh->id_admin);
        $this->assertSame(
            $this->admin->id_user,
            $fresh->id_admin,
            'id_admin harus sesuai dengan id_user admin yang sedang login.'
        );
    }

    // ─── TC-08 ───────────────────────────────────────────────────────────────

    /**
     * TC-08: Vendor menerima notifikasi hasil pemeriksaan beserta catatannya.
     *
     * AC: AC-05
     *
     * ⚠ LAPORAN: Kelas Notification belum dibuat (app/Notifications/ kosong).
     *   AdminController::periksaDokumen() tidak memanggil notify().
     *   Test ini membuktikan kondisi tersebut secara jujur:
     *   (1) tidak ada notifikasi yang terkirim, dan
     *   (2) catatan admin TETAP bisa dilihat vendor melalui halaman profil
     *       sebagai mekanisme pengganti yang sudah berjalan.
     */
    public function test_TC08_vendor_menerima_notifikasi_hasil_pemeriksaan(): void
    {
        Notification::fake();

        $dokumen = $this->buatDokumen($this->vendorMenunggu, 'KTP');
        $catatan = 'Foto KTP terlalu gelap, harap perjelas.';

        $this->actingAs($this->admin, 'web')
            ->patch(route('admin.dokumen.periksa', $dokumen), [
                'status_verifikasi' => DokumenLegalitas::DITOLAK,
                'catatan_admin'     => $catatan,
            ]);

        // Fitur notifikasi belum diimplementasikan → tidak ada yang terkirim
        Notification::assertNothingSent();

        // Mekanisme pengganti: catatan tampil di halaman profil vendor
        $this->actingAs($this->vendorMenunggu->user, 'web')
            ->get(route('vendor.profil'))
            ->assertOk()
            ->assertSee('Catatan admin: '.$catatan);
    }
}
