<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

/**
 * Tahap awal — registrasi & login dengan session.
 */
class Us00AutentikasiTest extends KolaboraTestCase
{
    public function test_skema_awal_users_dan_sessions(): void
    {
        $this->assertSame(
            ['id_user', 'nama', 'email', 'password_hash', 'no_hp', 'role', 'status_aktif', 'created_at'],
            Schema::getColumnListing('users'),
        );
        $this->assertSame(['id_pelanggan', 'alamat', 'foto_profile'], Schema::getColumnListing('pelanggan'));
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('administrator'));
        $this->assertTrue(Schema::hasTable('super_admin'));

        foreach (['percakapan_chat', 'pesan', 'kontrak_digital', 'pembayaran_escrow', 'sengketa', 'password_reset_tokens'] as $tabel) {
            $this->assertFalse(Schema::hasTable($tabel), "Tabel {$tabel} seharusnya tidak ada.");
        }
    }

    public function test_registrasi_pelanggan_membuat_user_dan_pelanggan(): void
    {
        $this->post('/register', [
            'role' => 'pelanggan', 'nama' => 'Rina', 'email' => 'rina@example.test', 'no_hp' => '081234567890',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/');

        $user = User::where('email', 'rina@example.test')->firstOrFail();
        $this->assertSame('pelanggan', $user->role);
        $this->assertTrue(password_verify('rahasia123', $user->password_hash));
        $this->assertDatabaseHas('pelanggan', ['id_pelanggan' => $user->id_user]);
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_registrasi_vendor_langsung_masuk_ke_portal_vendor(): void
    {
        $response = $this->post('/register', [
            'role' => 'vendor', 'nama' => 'Budi', 'email' => 'budi@example.test', 'no_hp' => '081234567891',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ]);
        $response->assertRedirect('/vendor/profil');

        $this->assertAuthenticatedAs(User::where('email', 'budi@example.test')->firstOrFail(), 'web');
        $this->get('/vendor/dashboard')->assertOk();
    }

    public function test_registrasi_divalidasi(): void
    {
        User::factory()->create(['email' => 'ada@example.test']);

        $this->post('/register', [
            'role' => 'admin', 'nama' => '', 'email' => 'ada@example.test', 'no_hp' => 'abc',
            'password' => 'pendek', 'password_confirmation' => 'beda',
        ])->assertSessionHasErrors(['role', 'nama', 'email', 'no_hp', 'password']);
        $this->assertGuest('web');
    }

    public function test_login_menyimpan_sesi_dan_logout_mengakhirinya(): void
    {
        $user = User::factory()->create(['email' => 'pel@example.test']);

        $this->post('/login', ['email' => 'pel@example.test', 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest('web');

        $this->post('/login', ['email' => 'pel@example.test', 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user, 'web');
        $this->get('/login')->assertRedirect();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest('web');
    }

    public function test_akun_nonaktif_tidak_dapat_masuk(): void
    {
        User::factory()->create(['email' => 'off@example.test', 'status_aktif' => false]);

        $this->post('/login', ['email' => 'off@example.test', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_halaman_dibatasi_sesuai_peran(): void
    {
        $this->get('/vendor/dashboard')->assertRedirect('/login');

        $pelanggan = User::factory()->create();
        $this->actingAs($pelanggan, 'web')->get('/vendor/dashboard')->assertForbidden();
        $this->actingAs($pelanggan, 'web')->get('/admin')->assertForbidden();
    }

    public function test_admin_dan_super_admin_masuk_lewat_halaman_login_yang_sama(): void
    {
        $this->buatAdmin('admin@example.test');
        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'password'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
        $this->post('/logout');

        $super = $this->buatSuperAdmin();
        $this->post('/login', ['email' => $super->email, 'password' => 'password'])->assertRedirect('/super-admin/dashboard');
        $this->get('/super-admin/dashboard')->assertOk();
    }
}
