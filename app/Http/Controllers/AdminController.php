<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Portal administrator: PBI-07 tinjau dokumen, PBI-08 status verifikasi,
 * PBI-16 tindak lanjut laporan, PBI-18 dashboard, PBI-19 hapus vendor.
 */
class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }
}
