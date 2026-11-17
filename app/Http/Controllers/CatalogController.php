<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PBI-04 katalog vendor terverifikasi, PBI-05 pencarian & filter,
 * PBI-06 detail vendor, PBI-14 perbandingan vendor.
 */
class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $vendors = $this->baseQuery()
            ->orderBy('nama_usaha')
            ->paginate(6)
            ->withQueryString();

        return view('customer.index', [
            'vendors' => $vendors,
            'kategori' => KategoriLayanan::orderBy('nama_kategori')->get(),
            'filters' => [],
        ]);
    }

    private function baseQuery()
    {
        return Vendor::terverifikasi()
            ->with([
                'kategori',
                'portofolio',
                'user',
            ]);
    }
}
