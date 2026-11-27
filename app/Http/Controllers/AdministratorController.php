<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * US-11 — administrator mengelola akun administrator lain: membuat, mengedit,
 * menonaktifkan/mengaktifkan kembali, dan menghapus. Minimal satu administrator harus tetap aktif.
 */
class AdministratorController extends Controller
{
    /** AC-01: membuat akun administrator baru yang langsung aktif. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], ['nama' => 'nama administrator', 'password' => 'kata sandi']);

        DB::transaction(function () use ($data): void {
            $admin = User::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password_hash' => $data['password'],
                'role' => User::ROLE_ADMIN,
                'status_aktif' => true,
            ]);
            $admin->administrator()->create(['level_admin' => 'operasional']);
        });

        return back()->with('status', 'Akun administrator '.$data['nama'].' berhasil dibuat.');
    }

    /** Mengedit nama, email, dan (opsional) kata sandi akun administrator. */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [], ['nama' => 'nama administrator', 'password' => 'kata sandi baru']);

        $perubahan = ['nama' => $data['nama'], 'email' => $data['email']];
        if (filled($data['password'] ?? null)) {
            $perubahan['password_hash'] = $data['password'];
        }

        DB::transaction(function () use ($user, $perubahan): void {
            $user->update($perubahan);
        });

        return back()->with('status', 'Akun administrator '.$user->nama.' berhasil diperbarui.');
    }

    /** AC-02 & AC-05: menonaktifkan administrator lain atau mengaktifkannya kembali. */
    public function ubahStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);
        $data = $request->validate(['status_aktif' => ['required', 'boolean']]);
        $aktif = (bool) $data['status_aktif'];
        $pelaku = $request->user();

        if (! $aktif) {
            if ($user->is($pelaku)) {
                return back()->withErrors(['status_aktif' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
            }
            if ($user->status_aktif && $this->jumlahAdminAktif() <= 1) {
                return back()->withErrors(['status_aktif' => 'Minimal satu akun administrator harus tetap aktif.']);
            }
        }

        if ($user->status_aktif === $aktif) {
            return back()->with('status', 'Akun administrator '.$user->nama.' sudah '.($aktif ? 'aktif.' : 'nonaktif.'));
        }

        DB::transaction(function () use ($user, $aktif): void {
            $user->update(['status_aktif' => $aktif]);
        });

        return back()->with('status', 'Akun administrator '.$user->nama.' '.($aktif ? 'diaktifkan kembali.' : 'dinonaktifkan.'));
    }

    /** AC-05: menghapus akun administrator lain; akun sendiri dan admin aktif terakhir tidak dapat dihapus. */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);
        $pelaku = $request->user();

        if ($user->is($pelaku)) {
            return back()->withErrors(['hapus' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }
        if ($user->status_aktif && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['hapus' => 'Minimal satu akun administrator harus tetap aktif.']);
        }

        DB::transaction(fn () => $user->delete());

        return back()->with('status', 'Akun administrator '.$user->nama.' telah dihapus.');
    }

    private function jumlahAdminAktif(): int
    {
        return User::where('role', User::ROLE_ADMIN)->where('status_aktif', true)->count();
    }
}
