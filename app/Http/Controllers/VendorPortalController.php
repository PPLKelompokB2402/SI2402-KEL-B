<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $user = $request->user();
        $vendors = $user->vendors()
            ->with([
                'kategori',
            ])
            ->orderBy('id_vendor')
            ->get();
        $vendor = $vendors->first();

        $checks = collect([
            ['label' => 'Profil usaha', 'complete' => (bool) $vendor],
            ['label' => 'Nomor WhatsApp', 'complete' => filled($user->no_hp)],
        ]);

        return view('vendor.dashboard', [
            'vendors' => $vendors,
            'vendor' => $vendor,
            'checks' => $checks,
            'kelengkapan' => (int) round($checks->where('complete', true)->count() / $checks->count() * 100),
            'stats' => [
                'usaha' => $vendors->count(),
                'menunggu' => $vendors->where('status_verifikasi', Vendor::MENUNGGU)->count(),
            ],
        ]);
    }

    /** Halaman "Usaha saya": daftar profil usaha dan form tambah usaha. */
    public function usaha(Request $request): View
    {
        return view('vendor.usaha', [
            'vendors' => $request->user()->vendors()->with([
                'kategori',
            ])->orderBy('id_vendor')->get(),
            'kategori' => KategoriLayanan::orderBy('nama_kategori')->get(),
        ]);
    }

    public function simpanUsaha(Request $request): RedirectResponse
    {
        $data = $this->validatedProfil($request);
        $user = $request->user();

        $vendor = DB::transaction(function () use ($user, $data) {
            $user->update(['no_hp' => $data['no_hp']]);
            $vendor = $user->vendors()->create([
                ...collect($data)->except('no_hp')->all(),
                'status_verifikasi' => Vendor::MENUNGGU,
            ]);

            return $vendor;
        });

        return redirect()->route('vendor.profil', ['vendor' => $vendor->id_vendor])
            ->with('status', 'Profil usaha ditambahkan dan diajukan untuk verifikasi.');
    }

    /** Halaman "Profil & legalitas" untuk satu usaha (dipilih lewat ?vendor=). */
    public function profil(Request $request): View|RedirectResponse
    {
        $vendors = $request->user()->vendors()->orderBy('id_vendor')->get();
        $vendor = $request->filled('vendor')
            ? $vendors->firstWhere('id_vendor', $request->integer('vendor'))
            : $vendors->first();

        if (! $vendor) {
            return redirect()->route('vendor.usaha')->with('status', 'Tambahkan profil usaha terlebih dahulu.');
        }

        return view('vendor.profil', [
            'vendors' => $vendors,
            'vendor' => $vendor,
            'kategori' => KategoriLayanan::orderBy('nama_kategori')->get(),
        ]);
    }

    public function updateUsaha(Request $request, Vendor $vendor): RedirectResponse
    {
        $this->authorizeOwner($request, $vendor);
        $data = $this->validatedProfil($request);
        $user = $request->user();

        DB::transaction(function () use ($user, $vendor, $data): void {
            $user->update(['no_hp' => $data['no_hp']]);
            // Setiap perubahan profil diajukan ulang untuk verifikasi admin.
            $vendor->update([...collect($data)->except('no_hp')->all(), 'status_verifikasi' => Vendor::MENUNGGU]);
        });

        return back()->with('status', 'Profil usaha disimpan dan diajukan untuk verifikasi.');
    }

    private function validatedProfil(Request $request): array
    {
        return $request->validate([
            'nama_usaha' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'exists:kategori_layanan,id_kategori'],
            'deskripsi_usaha' => ['required', 'string', 'max:3000'],
            'lokasi_layanan' => ['required', 'string', 'max:150'],
            'kisaran_harga' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
        ]);
    }

    private function authorizeOwner(Request $request, Vendor $vendor): void
    {
        abort_unless($vendor->id_user === $request->user()->id_user, 403);
    }
}
