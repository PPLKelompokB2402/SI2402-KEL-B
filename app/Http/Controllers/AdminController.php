<?php

namespace App\Http\Controllers;

use App\Models\DokumenLegalitas;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    public function verifikasi(): View
    {
        return view('admin.verifikasi', [
            'vendors' => Vendor::with(['kategori', 'user', 'dokumenLegalitas', 'portofolio'])
                ->where('status_verifikasi', Vendor::MENUNGGU)
                ->get()
                ->sortBy(fn ($vendor) => $vendor->user->created_at),
            'updatedAt' => now(),
        ]);
    }

    public function detailVerifikasi(Vendor $vendor): View
    {
        $vendor->load(['kategori', 'user', 'dokumenLegalitas.administrator.user', 'portofolio']);

        return view('admin.verifikasi-detail', [
            'vendor' => $vendor,
            'jenisDokumen' => DokumenLegalitas::JENIS,
        ]);
    }

    public function periksaDokumen(Request $request, DokumenLegalitas $dokumen): RedirectResponse
    {
        $data = $request->validate([
            'status_verifikasi' => ['required', Rule::in([DokumenLegalitas::VALID, DokumenLegalitas::DITOLAK])],
            'catatan_admin' => ['nullable', 'string', 'max:500', Rule::requiredIf($request->input('status_verifikasi') === DokumenLegalitas::DITOLAK)],
        ]);
        $admin = $request->user();

        DB::transaction(function () use ($dokumen, $data, $admin): void {
            $dokumen->update([...$data, 'id_admin' => $admin->id_user]);
        });

        return back()->with('status', 'Hasil pemeriksaan dokumen disimpan.');
    }

    public function ubahStatusVendor(Request $request, Vendor $vendor): RedirectResponse
    {
        $data = $request->validate([
            'status_verifikasi' => ['required', Rule::in(array_keys(Vendor::STATUS_LABEL))],
        ]);
        $verifikasi = $data['status_verifikasi'] === Vendor::TERVERIFIKASI;

        if ($verifikasi && ! $this->dokumenLengkap($vendor)) {
            return back()->withErrors(['status_verifikasi' => 'Vendor baru dapat diverifikasi jika dokumen KTP dan NIB/SIUP sudah diunggah dan tidak ada yang ditolak.']);
        }

        $admin = $request->user();
        DB::transaction(function () use ($vendor, $data, $verifikasi, $admin): void {
            if ($verifikasi) {
                // Dokumen yang belum diperiksa ikut dinyatakan valid oleh admin yang memverifikasi.
                $vendor->dokumenLegalitas()->where('status_verifikasi', DokumenLegalitas::MENUNGGU)
                    ->update(['status_verifikasi' => DokumenLegalitas::VALID, 'id_admin' => $admin->id_user]);
            }
            $vendor->update(['status_verifikasi' => $data['status_verifikasi']]);
        });

        return redirect()->route('admin.verifikasi')->with('status', 'Status verifikasi '.$vendor->nama_usaha.' diperbarui.');
    }

    /** KTP dan NIB/SIUP sudah diunggah dan tidak ada yang ditolak. */
    private function dokumenLengkap(Vendor $vendor): bool
    {
        $dokumen = $vendor->dokumenLegalitas()->get();
        $aktif = $dokumen->where('status_verifikasi', '!=', DokumenLegalitas::DITOLAK)->pluck('jenis_dokumen');

        return $aktif->contains('KTP') && $aktif->intersect(['NIB', 'SIUP'])->isNotEmpty();
    }
}
