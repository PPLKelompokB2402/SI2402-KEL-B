<?php

namespace Tests\Feature;

use App\Models\User;

/**
 * US-11 (frontend) — tombol menonaktifkan/mengaktifkan dan menghapus akun administrator di portal super admin.
 */
class Us11dNonaktifHapusAdministratorTest extends KolaboraTestCase
{
    public function test_ac02_tombol_nonaktifkan_dan_hapus_tampil_untuk_setiap_admin(): void
    {
        $admin = $this->buatAdmin();

        $this->sebagaiSuperAdmin($this->buatSuperAdmin())->get('/super-admin/administrator')->assertOk()
            ->assertSee('action="'.route('super-admin.administrator.status', $admin).'"', false)
            ->assertSee('action="'.route('super-admin.administrator.destroy', $admin).'"', false)
            ->assertSee('Nonaktifkan')->assertSee('>Hapus</button>', false)
            ->assertSee('return confirm(', false);
    }

    public function test_ac02_menonaktifkan_lalu_mengaktifkan_kembali_dari_daftar(): void
    {
        $this->buatAdmin();
        $lain = $this->buatAdmin();
        $this->sebagaiSuperAdmin($this->buatSuperAdmin());

        $this->from('/super-admin/administrator')->patch("/super-admin/administrator/{$lain->id_user}/status", ['status_aktif' => 0])
            ->assertRedirect('/super-admin/administrator');
        $this->get('/super-admin/administrator')
            ->assertSee("Akun administrator {$lain->nama} dinonaktifkan.")
            ->assertSee('<input type="hidden" name="status_aktif" value="1">', false)
            ->assertSee('>Aktifkan</button>', false);

        $this->from('/super-admin/administrator')->patch("/super-admin/administrator/{$lain->id_user}/status", ['status_aktif' => 1]);
        $this->get('/super-admin/administrator')->assertSee("Akun administrator {$lain->nama} diaktifkan kembali.");
        $this->assertTrue($lain->fresh()->status_aktif);
    }

    public function test_menghapus_admin_dari_daftar(): void
    {
        $this->buatAdmin();
        $lain = $this->buatAdmin('dihapus@kolaboraeo.test');
        $this->sebagaiSuperAdmin($this->buatSuperAdmin());

        $this->from('/super-admin/administrator')->delete("/super-admin/administrator/{$lain->id_user}")->assertRedirect('/super-admin/administrator');
        $this->get('/super-admin/administrator')
            ->assertSee("Akun administrator {$lain->nama} telah dihapus.")
            ->assertDontSee('dihapus@kolaboraeo.test');
    }

    public function test_ac05_pesan_kesalahan_tampil_saat_menghilangkan_admin_aktif_terakhir(): void
    {
        User::where('role', User::ROLE_ADMIN)->delete();
        $satuSatunya = $this->buatAdmin();
        $this->sebagaiSuperAdmin($this->buatSuperAdmin());

        $this->from('/super-admin/administrator')->patch("/super-admin/administrator/{$satuSatunya->id_user}/status", ['status_aktif' => 0])
            ->assertRedirect('/super-admin/administrator');
        $this->get('/super-admin/administrator')->assertSee('Minimal satu akun administrator harus tetap aktif.');
        $this->from('/super-admin/administrator')->delete("/super-admin/administrator/{$satuSatunya->id_user}");
        $this->get('/super-admin/administrator')->assertSee('Minimal satu akun administrator harus tetap aktif.');
        $this->assertTrue($satuSatunya->fresh()->status_aktif);
    }
}
