<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Portal super admin. Kelola akun administrator (US-11) ada di AdministratorController
 * (relasi "Mengelola" SuperAdmin 1:N Administrator).
 */
class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        return view('super-admin.dashboard');
    }
}
