<?php

namespace App\Http\Controllers;

use App\Models\DokumenLegalitas;
use App\Models\Portofolio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Menyajikan file dari storage privat tanpa perlu `storage:link`.
 * Hanya versi ber-watermark portofolio yang bersifat publik.
 */
class MediaController extends Controller
{
    public function portofolio(Portofolio $portofolio): StreamedResponse
    {
        return $this->stream($portofolio->media_watermark_url, true);
    }

    public function dokumen(DokumenLegalitas $dokumen): StreamedResponse
    {
        abort_unless($this->isPetugas() || Auth::guard('web')->id() === $dokumen->vendor->id_user, 403);

        return $this->stream($dokumen->media_url);
    }

    private function isPetugas(): bool
    {
        $admin = Auth::guard('admin')->user();
        $super = Auth::guard('super_admin')->user();

        return ($admin?->role === User::ROLE_ADMIN && $admin->status_aktif)
            || ($super?->role === User::ROLE_SUPER_ADMIN && $super->status_aktif);
    }

    private function stream(string $path, bool $public = false): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => $public ? 'public, max-age=86400' : 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
