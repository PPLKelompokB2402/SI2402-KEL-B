<?php

namespace Tests\Feature;

/**
 * US-11 (frontend) — halaman mengedit akun administrator di portal super admin.
 */
class Us11cMengeditAdministratorTest extends KolaboraTestCase
{
    public function test_tombol_edit_tampil_di_daftar_dan_membuka_form_terisi(): void
    {
        $target = $this->buatAdmin('target@kolaboraeo.test');
        $target->update(['nama' => 'Target Admin']);

        $this->sebagaiSuperAdmin($this->buatSuperAdmin())->get('/super-admin/administrator')
            ->assertSee('href="'.route('super-admin.administrator.edit', $target).'"', false);

        $this->get("/super-admin/administrator/{$target->id_user}/edit")->assertOk()
            ->assertSee('Edit akun administrator')
            ->assertSee('value="Target Admin"', false)
            ->assertSee('value="target@kolaboraeo.test"', false)
            ->assertSee('kosongkan bila tidak diganti');
    }

    public function test_simpan_perubahan_kembali_ke_daftar_dengan_pesan(): void
    {
        $target = $this->buatAdmin('target@kolaboraeo.test');

        $this->sebagaiSuperAdmin($this->buatSuperAdmin())->put("/super-admin/administrator/{$target->id_user}", [
            'nama' => 'Target Diubah', 'email' => 'diubah@kolaboraeo.test',
        ])->assertRedirect('/super-admin/administrator');

        $this->get('/super-admin/administrator')
            ->assertSee('Akun administrator Target Diubah berhasil diperbarui.')
            ->assertSee('diubah@kolaboraeo.test')
            ->assertDontSee('target@kolaboraeo.test');
    }

    public function test_kesalahan_validasi_kembali_ke_form_edit_dengan_input_lama(): void
    {
        $this->buatAdmin('lain@kolaboraeo.test');
        $target = $this->buatAdmin('target@kolaboraeo.test');
        $edit = "/super-admin/administrator/{$target->id_user}/edit";

        $this->sebagaiSuperAdmin($this->buatSuperAdmin())->from($edit)->put("/super-admin/administrator/{$target->id_user}", [
            'nama' => 'Nama Coba', 'email' => 'lain@kolaboraeo.test',
        ])->assertRedirect($edit)->assertSessionHasErrors('email');

        $this->get($edit)->assertSee('value="Nama Coba"', false);
        $this->assertSame('target@kolaboraeo.test', $target->fresh()->email);
    }

    public function test_halaman_edit_hanya_untuk_akun_administrator_dan_super_admin(): void
    {
        $vendor = $this->buatVendor()->user;
        $target = $this->buatAdmin();

        $this->sebagaiSuperAdmin($this->buatSuperAdmin())->get("/super-admin/administrator/{$vendor->id_user}/edit")->assertNotFound();
        $this->sebagaiAdmin($this->buatAdmin());
        auth('super_admin')->logout();
        $this->get("/super-admin/administrator/{$target->id_user}/edit")->assertRedirect('/super-admin/login');
    }
}
