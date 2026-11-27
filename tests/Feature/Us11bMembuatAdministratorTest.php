<?php

namespace Tests\Feature;

use App\Models\User;

/**
 * US-11 (frontend) — halaman daftar administrator dan form membuat akun di portal super admin.
 */
class Us11bMembuatAdministratorTest extends KolaboraTestCase
{
    public function test_halaman_kelola_administrator_menampilkan_daftar_dan_form_buat(): void
    {
        $this->buatAdmin('aktif@kolaboraeo.test');
        $nonaktif = $this->buatAdmin('nonaktif@kolaboraeo.test');
        $nonaktif->update(['status_aktif' => false]);

        $this->sebagaiSuperAdmin($this->buatSuperAdmin())->get('/super-admin/dashboard')->assertSee(route('super-admin.administrator'), false);
        $this->get('/super-admin/administrator')->assertOk()
            ->assertSee('Kelola administrator')
            ->assertSee('＋ Buat akun administrator')
            ->assertSee('action="'.route('super-admin.administrator.store').'"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('aktif@kolaboraeo.test')
            ->assertSee('nonaktif@kolaboraeo.test')->assertSee('Nonaktif');
    }

    public function test_menu_kelola_administrator_tidak_ada_di_portal_admin(): void
    {
        $this->sebagaiAdmin($this->buatAdmin())->get('/admin')->assertOk()->assertDontSee('/administrator', false);
    }

    public function test_ac01_super_admin_membuat_akun_lewat_form_dan_akun_tampil_di_daftar(): void
    {
        $this->sebagaiSuperAdmin($this->buatSuperAdmin());
        $aktifAwal = User::where('role', User::ROLE_ADMIN)->where('status_aktif', true)->count();

        $this->from('/super-admin/administrator')->post('/super-admin/administrator', [
            'nama' => 'Rina Admin', 'email' => 'rina@kolaboraeo.test', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/super-admin/administrator');

        $this->get('/super-admin/administrator')
            ->assertSee('Akun administrator Rina Admin berhasil dibuat.')
            ->assertSee('Rina Admin')->assertSee('rina@kolaboraeo.test')
            ->assertSee(($aktifAwal + 1).' aktif');
    }

    public function test_ac01_kesalahan_form_ditampilkan_dan_form_tetap_terbuka(): void
    {
        $this->buatAdmin('ada@kolaboraeo.test');
        $this->sebagaiSuperAdmin($this->buatSuperAdmin());

        $this->from('/super-admin/administrator')->post('/super-admin/administrator', [
            'nama' => 'Budi', 'email' => 'ada@kolaboraeo.test', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/super-admin/administrator');

        $this->get('/super-admin/administrator')
            ->assertSee('class="category-add-details"  open', false)
            ->assertSee('value="Budi"', false);
    }

    public function test_halaman_kelola_admin_hanya_untuk_super_admin(): void
    {
        $this->get('/super-admin/administrator')->assertRedirect('/super-admin/login');
        $this->actingAs($this->buatVendor()->user, 'web')->get('/super-admin/administrator')->assertRedirect('/super-admin/login');
        $this->sebagaiAdmin($this->buatAdmin())->get('/super-admin/administrator')->assertRedirect('/super-admin/login');
    }
}
