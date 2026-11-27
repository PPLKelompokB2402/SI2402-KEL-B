<?php

namespace Tests\Feature;

use App\Models\DokumenLegalitas;
use App\Models\User;
use App\Models\Vendor;

/**
 * US-11 (backend) — logika membuat, mengedit, menonaktifkan, dan menghapus akun administrator.
 */
class Us11aKelolaAdministratorBackendTest extends KolaboraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mulai dari kondisi tanpa admin bawaan migrasi agar jumlah admin aktif mudah dihitung.
        User::where('role', User::ROLE_ADMIN)->delete();
    }

    private function dataAdmin(array $lain = []): array
    {
        return array_merge(['nama' => 'Rina Admin', 'email' => 'rina@kolaboraeo.test', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'], $lain);
    }

    public function test_ac01_admin_membuat_akun_administrator_yang_langsung_bisa_login(): void
    {
        $this->sebagaiAdmin($this->buatAdmin())->post('/admin/administrator', $this->dataAdmin())
            ->assertSessionHas('status', 'Akun administrator Rina Admin berhasil dibuat.');

        $baru = User::where('email', 'rina@kolaboraeo.test')->firstOrFail();
        $this->assertSame(User::ROLE_ADMIN, $baru->role);
        $this->assertTrue($baru->status_aktif);
        $this->assertNotNull($baru->administrator);

        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'rina@kolaboraeo.test', 'password' => 'rahasia123'])->assertRedirect('/admin');
    }

    public function test_ac01_validasi_membuat_admin(): void
    {
        $this->sebagaiAdmin($this->buatAdmin('ada@kolaboraeo.test'));

        $this->post('/admin/administrator', $this->dataAdmin(['nama' => '', 'email' => 'ada@kolaboraeo.test', 'password' => '123', 'password_confirmation' => '456']))
            ->assertSessionHasErrors(['nama', 'email', 'password']);
        $this->post('/admin/administrator', $this->dataAdmin(['email' => 'bukan-email']))->assertSessionHasErrors('email');
        $this->post('/admin/administrator', $this->dataAdmin(['password_confirmation' => 'berbeda123']))->assertSessionHasErrors('password');
        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->count());
    }

    public function test_admin_mengedit_nama_email_dan_kata_sandi_admin_lain(): void
    {
        $target = $this->buatAdmin('lama@kolaboraeo.test');

        $this->sebagaiAdmin($this->buatAdmin())->put("/admin/administrator/{$target->id_user}", [
            'nama' => 'Nama Baru', 'email' => 'baru@kolaboraeo.test', 'password' => '', 'password_confirmation' => '',
        ])->assertSessionHasNoErrors()->assertSessionHas('status', 'Akun administrator Nama Baru berhasil diperbarui.');
        $this->assertSame(['Nama Baru', 'baru@kolaboraeo.test'], [$target->fresh()->nama, $target->fresh()->email]);

        // Kata sandi kosong tidak mengubah kata sandi lama; bila diisi, kata sandi diganti.
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'baru@kolaboraeo.test', 'password' => 'password'])->assertRedirect('/admin');
        $this->post('/admin/logout');
        $this->sebagaiAdmin($this->buatAdmin())->put("/admin/administrator/{$target->id_user}", [
            'nama' => 'Nama Baru', 'email' => 'baru@kolaboraeo.test', 'password' => 'sandiBaru123', 'password_confirmation' => 'sandiBaru123',
        ])->assertSessionHasNoErrors();
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'baru@kolaboraeo.test', 'password' => 'sandiBaru123'])->assertRedirect('/admin');
    }

    public function test_validasi_mengedit_admin(): void
    {
        $admin = $this->buatAdmin('a@kolaboraeo.test');
        $target = $this->buatAdmin('b@kolaboraeo.test');
        $this->sebagaiAdmin($admin);

        $this->put("/admin/administrator/{$target->id_user}", ['nama' => '', 'email' => 'a@kolaboraeo.test'])
            ->assertSessionHasErrors(['nama', 'email']);
        $this->put("/admin/administrator/{$target->id_user}", ['nama' => 'B', 'email' => 'b@kolaboraeo.test', 'password' => 'pendek', 'password_confirmation' => 'pendek'])
            ->assertSessionHasErrors('password');
        // Email milik sendiri boleh dipakai lagi saat mengedit.
        $this->put("/admin/administrator/{$target->id_user}", ['nama' => 'B Baru', 'email' => 'b@kolaboraeo.test'])->assertSessionHasNoErrors();

        // Hanya akun administrator yang dapat diedit lewat fitur ini.
        $vendor = $this->buatVendor()->user;
        $this->put("/admin/administrator/{$vendor->id_user}", ['nama' => 'X', 'email' => 'x@kolaboraeo.test'])->assertNotFound();
    }

    public function test_ac02_admin_menonaktifkan_admin_lain_dan_yang_nonaktif_tidak_bisa_masuk(): void
    {
        $adminA = $this->buatAdmin();
        $adminB = $this->buatAdmin('b@kolaboraeo.test');

        $this->sebagaiAdmin($adminA)->patch("/admin/administrator/{$adminB->id_user}/status", ['status_aktif' => 0])
            ->assertSessionHas('status', "Akun administrator {$adminB->nama} dinonaktifkan.");
        $this->assertFalse($adminB->fresh()->status_aktif);

        // Sesi yang masih terbuka ditolak, dan login baru gagal.
        $this->sebagaiAdmin($adminB->fresh())->get('/admin')->assertForbidden();
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'b@kolaboraeo.test', 'password' => 'password'])->assertSessionHasErrors('email');

        // Dapat diaktifkan kembali.
        $this->sebagaiAdmin($adminA)->patch("/admin/administrator/{$adminB->id_user}/status", ['status_aktif' => 1])
            ->assertSessionHas('status', "Akun administrator {$adminB->nama} diaktifkan kembali.");
        $this->assertTrue($adminB->fresh()->status_aktif);
    }

    public function test_admin_menghapus_akun_admin_lain(): void
    {
        $adminA = $this->buatAdmin();
        $adminB = $this->buatAdmin('b@kolaboraeo.test');

        $this->sebagaiAdmin($adminA)->delete("/admin/administrator/{$adminB->id_user}")
            ->assertSessionHas('status', "Akun administrator {$adminB->nama} telah dihapus.");
        $this->assertNull(User::find($adminB->id_user));
        $this->assertDatabaseMissing('administrator', ['id_admin' => $adminB->id_user]);

        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'b@kolaboraeo.test', 'password' => 'password'])->assertSessionHasErrors('email');
    }

    public function test_ac03_beberapa_admin_aktif_memiliki_hak_akses_setara(): void
    {
        $adminA = $this->buatAdmin('a@kolaboraeo.test');
        $adminB = $this->buatAdmin('b@kolaboraeo.test');
        $vendorA = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        $vendorB = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);

        // Kedua admin login bersamaan dan sama-sama dapat mengerjakan antrean verifikasi.
        foreach ([[$adminA, $vendorA], [$adminB, $vendorB]] as [$admin, $vendor]) {
            $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
            $this->get('/admin/verifikasi')->assertOk();
            $this->patch("/admin/vendors/{$vendor->id_vendor}/status", ['status_verifikasi' => Vendor::DITOLAK])->assertSessionHasNoErrors();
            $this->post('/admin/administrator', $this->dataAdmin(['email' => "dibuat.{$admin->id_user}@kolaboraeo.test"]))->assertSessionHasNoErrors();
            $this->post('/admin/logout');
        }
        $this->assertSame([Vendor::DITOLAK, Vendor::DITOLAK], [$vendorA->fresh()->status_verifikasi, $vendorB->fresh()->status_verifikasi]);
    }

    public function test_ac04_antrean_verifikasi_dilanjutkan_admin_lain_saat_satu_admin_nonaktif(): void
    {
        $adminA = $this->buatAdmin();
        $adminB = $this->buatAdmin();
        $vendor = $this->buatVendor(['status_verifikasi' => Vendor::MENUNGGU]);
        foreach (['KTP', 'NIB'] as $i => $jenis) {
            $vendor->dokumenLegalitas()->create([
                'jenis_dokumen' => $jenis, 'no_dokumen' => (string) ($i + 1), 'media_url' => "legalitas/{$jenis}.pdf",
                'status_verifikasi' => DokumenLegalitas::VALID,
            ]);
        }

        $this->sebagaiAdmin($adminA)->patch("/admin/administrator/{$adminB->id_user}/status", ['status_aktif' => 0]);

        $this->get('/admin/verifikasi')->assertOk()->assertSee($vendor->nama_usaha);
        $this->patch("/admin/vendors/{$vendor->id_vendor}/status", ['status_verifikasi' => Vendor::TERVERIFIKASI])->assertSessionHasNoErrors();
        $this->assertSame(Vendor::TERVERIFIKASI, $vendor->fresh()->status_verifikasi);
    }

    public function test_ac05_minimal_satu_admin_tetap_aktif(): void
    {
        $adminA = $this->buatAdmin();
        $adminB = $this->buatAdmin();
        $this->sebagaiAdmin($adminA);

        // Tidak bisa menonaktifkan atau menghapus diri sendiri.
        $this->patch("/admin/administrator/{$adminA->id_user}/status", ['status_aktif' => 0])
            ->assertSessionHasErrors(['status_aktif' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        $this->delete("/admin/administrator/{$adminA->id_user}")
            ->assertSessionHasErrors(['hapus' => 'Anda tidak dapat menghapus akun Anda sendiri.']);

        // B dinonaktifkan lalu dihapus; A tetap satu-satunya admin aktif dan tidak bisa menghilangkan dirinya.
        $this->patch("/admin/administrator/{$adminB->id_user}/status", ['status_aktif' => 0])->assertSessionHasNoErrors();
        $this->delete("/admin/administrator/{$adminB->id_user}")->assertSessionHasNoErrors();
        $this->patch("/admin/administrator/{$adminA->id_user}/status", ['status_aktif' => 0])->assertSessionHasErrors('status_aktif');
        $this->delete("/admin/administrator/{$adminA->id_user}")->assertSessionHasErrors('hapus');
        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->where('status_aktif', true)->count());
    }

    public function test_ac06_admin_tidak_bisa_didaftarkan_lewat_registrasi_publik(): void
    {
        $this->get('/register')->assertOk()->assertDontSee('value="admin"', false);
        $this->post('/register', [
            'nama' => 'Penyusup', 'email' => 'penyusup@kolaboraeo.test', 'no_hp' => '081234567890',
            'role' => 'admin', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'penyusup@kolaboraeo.test']);
    }

    public function test_aksi_kelola_admin_hanya_untuk_administrator(): void
    {
        $target = $this->buatAdmin('target@kolaboraeo.test');
        $permintaan = [
            ['post', '/admin/administrator', $this->dataAdmin()],
            ['put', "/admin/administrator/{$target->id_user}", ['nama' => 'X', 'email' => 'x@kolaboraeo.test']],
            ['patch', "/admin/administrator/{$target->id_user}/status", ['status_aktif' => 0]],
            ['delete', "/admin/administrator/{$target->id_user}", []],
        ];

        foreach ([null, $this->buatVendor()->user, $this->buatPelanggan()] as $pengguna) {
            if ($pengguna) {
                $this->actingAs($pengguna, 'web');
            }
            foreach ($permintaan as [$metode, $url, $data]) {
                $this->{$metode}($url, $data)->assertRedirect('/admin/login');
            }
        }
        $this->assertDatabaseMissing('users', ['email' => 'rina@kolaboraeo.test']);
        $this->assertTrue($target->fresh()->status_aktif);
    }
}
