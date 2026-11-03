<?php

namespace App\Http\Controllers;

use App\Models\DokumenLegalitas;
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
    public function dokumen(DokumenLegalitas $dokumen): StreamedResponse
    {
        abort_unless($this->isPetugas() || Auth::guard('web')->id() === $dokumen->vendor->id_user, 403);

        return $this->stream($dokumen->media_url);
    }

    private function isPetugas(): bool
    {
        // Sebelum PBI-10 semua peran masuk lewat guard "web".
        $user = Auth::guard('web')->user();

        return $user !== null && $user->status_aktif
            && in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN], true);
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
