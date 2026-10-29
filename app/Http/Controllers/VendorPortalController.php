<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portal vendor (satu akun dapat mengelola beberapa profil usaha):
 * PBI-01 profil usaha, PBI-02 unggah legalitas, PBI-03 portofolio + watermark,
 * PBI-13 ketersediaan.
 */
class VendorPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        return view('vendor.dashboard', ['user' => $request->user()]);
    }
}
