<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Login (satu halaman untuk semua peran) dan registrasi akun pelanggan/vendor.
 * Sesi login disimpan di tabel sessions (SESSION_DRIVER=database).
 */
class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        return $this->authenticate($request, 'web', [User::ROLE_PELANGGAN, User::ROLE_VENDOR, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN]);
    }

    private function authenticate(Request $request, string $guard, array $roles): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $csrfToken = $request->session()->token();

        if (! Auth::guard($guard)->attempt($credentials + ['status_aktif' => true])) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak cocok, atau akun sedang nonaktif.'])->onlyInput('email');
        }

        $user = Auth::guard($guard)->user();
        if (! in_array($user->role, $roles, true)) {
            Auth::guard($guard)->logout();
            $request->session()->put('_token', $csrfToken);

            return back()->withErrors(['email' => 'Akun ini tidak memiliki akses melalui halaman masuk tersebut.'])->onlyInput('email');
        }

        $request->session()->put('_token', $csrfToken);

        return redirect()->to($this->dashboardFor($user));
    }

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'role' => ['required', Rule::in([User::ROLE_PELANGGAN, User::ROLE_VENDOR])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'role' => $data['role'],
                'password_hash' => $data['password'],
                'status_aktif' => true,
            ]);

            if ($user->role === User::ROLE_PELANGGAN) {
                $user->pelanggan()->create([]);
            }

            return $user;
        });

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return $user->role === User::ROLE_VENDOR
            ? redirect()->route('vendor.profil')->with('status', 'Akun vendor dibuat. Lengkapi profil usaha agar dapat diverifikasi.')
            : redirect()->route('vendors.index')->with('status', 'Akun pelanggan berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        return $this->logoutGuard($request, 'web');
    }

    private function logoutGuard(Request $request, string $guard): RedirectResponse
    {
        Auth::guard($guard)->logout();
        $request->session()->migrate(true);

        return redirect()->to(match ($guard) {
            default => route('vendors.index'),
        });
    }

    private function dashboardFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_VENDOR => route('vendor.dashboard'),
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_SUPER_ADMIN => route('super-admin.dashboard'),
            default => route('vendors.index'),
        };
    }
}
