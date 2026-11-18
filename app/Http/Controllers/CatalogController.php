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
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'integer'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'harga_min' => ['nullable', 'numeric', 'min:0'],
            'harga_max' => ['nullable', 'numeric', 'min:0', 'gte:harga_min'],
            'urut' => ['nullable', 'in:relevan,harga'],
        ]);

        $vendors = $this->baseQuery()
            ->when($filters['q'] ?? null, function ($query, $term) {
                $query->where(function ($search) use ($term) {
                    $search->where('nama_usaha', 'like', "%{$term}%")
                        ->orWhere('deskripsi_usaha', 'like', "%{$term}%")
                        ->orWhereHas('kategori', fn ($kategori) => $kategori->where('nama_kategori', 'like', "%{$term}%"));
                });
            })
            ->when($filters['kategori'] ?? null, fn ($query, $id) => $query->where('id_kategori', $id))
            ->when($filters['lokasi'] ?? null, fn ($query, $lokasi) => $query->where('lokasi_layanan', 'like', "%{$lokasi}%"))
            ->when(isset($filters['harga_min']), fn ($query) => $query->where('kisaran_harga', '>=', $filters['harga_min']))
            ->when(isset($filters['harga_max']), fn ($query) => $query->where('kisaran_harga', '<=', $filters['harga_max']))
            ->when(($filters['urut'] ?? null) === 'harga', fn ($query) => $query->orderBy('kisaran_harga'))
            ->orderBy('nama_usaha')
            ->paginate(6)
            ->withQueryString();

        return view('customer.index', [
            'vendors' => $vendors,
            'kategori' => KategoriLayanan::orderBy('nama_kategori')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, Vendor $vendor): View
    {
        return $this->detail($request, $vendor, 'show');
    }

    public function portofolio(Request $request, Vendor $vendor): View
    {
        return $this->detail($request, $vendor, 'portofolio');
    }

    public function harga(Request $request, Vendor $vendor): View
    {
        return $this->detail($request, $vendor, 'harga');
    }

    private function detail(Request $request, Vendor $vendor, string $tab): View
    {
        abort_unless($vendor->status_verifikasi === Vendor::TERVERIFIKASI, 404);

        $vendor->load([
            'kategori',
            'user',
            'portofolio',
        ]);

        return view('customer.'.$tab, [
            'vendor' => $vendor,
            'activeTab' => $tab,
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
