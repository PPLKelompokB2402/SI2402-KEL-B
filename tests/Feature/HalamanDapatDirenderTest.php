<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Uji asap: semua halaman GET tanpa parameter pada tahap ini dapat dirender oleh peran yang berhak.
 */
class HalamanDapatDirenderTest extends KolaboraTestCase
{
    public function test_semua_halaman_tanpa_parameter_dapat_dirender(): void
    {
        $vendorUser = \App\Models\User::factory()->role('vendor')->create();
        $pelanggan = $this->buatPelanggan();
        $admin = $this->buatAdmin();
        $super = $this->buatSuperAdmin();

        $diperiksa = 0;
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || str_contains($uri, '{') || Str::startsWith($uri, ['_', 'up', 'storage', 'sanctum'])
                || Str::endsWith($uri, ['export', 'login', 'register'])) {
                continue;
            }

            auth('web')->logout();
            if (Str::startsWith($uri, 'vendor/')) {
                $this->actingAs($vendorUser, 'web');
            } elseif (Str::startsWith($uri, 'admin')) {
                $this->sebagaiAdmin($admin);
            } elseif (Str::startsWith($uri, 'super-admin')) {
                $this->sebagaiSuperAdmin($super);
            } elseif ($uri === 'notifikasi') {
                $this->actingAs($pelanggan, 'web');
            }

            $this->get('/'.ltrim($uri, '/'))->assertOk();
            $diperiksa++;
        }

        $this->assertGreaterThan(0, $diperiksa);
        foreach (['/login', '/register'] as $uri) {
            auth('web')->logout();
            $this->get($uri)->assertOk();
        }
    }
}
