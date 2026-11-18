<?php

namespace Tests\Feature;

use App\Models\User;

/**
 * PBI-10 — portal login administrator dan super admin terpisah.
 */
class Us10PortalAdminTest extends KolaboraTestCase
{
    public function test_portal_admin_terpisah_dan_peran_tidak_bisa_silang(): void
    {
        $admin = $this->buatAdmin('admin@example.test');
        User::factory()->create(['email' => 'pel@example.test']);

        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/super-admin/dashboard')->assertRedirect('/super-admin/login');
        $this->get('/admin/login')->assertOk();
        $this->get('/super-admin/login')->assertOk();

        // Admin tidak bisa masuk lewat login pelanggan/vendor, dan sebaliknya.
        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => 'pel@example.test', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->post('/super-admin/login', ['email' => 'admin@example.test', 'password' => 'password'])->assertSessionHasErrors('email');

        $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertGuest('web');

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }

    public function test_sesi_admin_tidak_membuka_portal_super_admin(): void
    {
        $admin = $this->buatAdmin();
        $this->actingAs($admin, 'admin')->get('/super-admin/dashboard')->assertRedirect('/super-admin/login');

        $super = $this->buatSuperAdmin();
        $this->post('/super-admin/login', ['email' => $super->email, 'password' => 'password'])->assertRedirect('/super-admin/dashboard');
        $this->get('/super-admin/dashboard')->assertOk();
    }
}
