<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * US-11 — super admin mengelola akun administrator: membuat, mengedit,
 * menonaktifkan/mengaktifkan kembali, dan menghapus. Minimal satu administrator harus tetap aktif.
 */
class AdministratorController extends Controller
{
    /** Daftar administrator beserta form pembuatan akun. */
    public function index(): View
    {
        return view('super-admin.administrator', [
            'admins' => User::where('role', User::ROLE_ADMIN)
                ->orderByDesc('status_aktif')
                ->orderBy('nama')
                ->get(),
            'jumlahAktif' => $this->jumlahAdminAktif(),
        ]);
    }

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

    /** Halaman edit akun administrator. */
    public function edit(User $user): View
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);

        return view('super-admin.administrator-edit', ['admin' => $user]);
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

        return redirect()->route('super-admin.administrator')->with('status', 'Akun administrator '.$user->nama.' berhasil diperbarui.');
    }

    /** AC-02 & AC-05: menonaktifkan administrator atau mengaktifkannya kembali; admin aktif terakhir tidak bisa dinonaktifkan. */
    public function ubahStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);
        $data = $request->validate(['status_aktif' => ['required', 'boolean']]);
        $aktif = (bool) $data['status_aktif'];

        if (! $aktif && $user->status_aktif && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['status_aktif' => 'Minimal satu akun administrator harus tetap aktif.']);
        }

        if ($user->status_aktif === $aktif) {
            return back()->with('status', 'Akun administrator '.$user->nama.' sudah '.($aktif ? 'aktif.' : 'nonaktif.'));
        }

        DB::transaction(function () use ($user, $aktif): void {
            $user->update(['status_aktif' => $aktif]);
        });

        return back()->with('status', 'Akun administrator '.$user->nama.' '.($aktif ? 'diaktifkan kembali.' : 'dinonaktifkan.'));
    }

    /** AC-05: menghapus akun administrator; admin aktif terakhir tidak dapat dihapus. */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_ADMIN, 404);

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
