<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Portal super admin: PBI-11 multi-admin dan PBI-12 kelola akun administrator
 * (relasi "Mengelola" SuperAdmin 1:N Administrator).
 */
class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        return view('super-admin.dashboard');
    }
}
