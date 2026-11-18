<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * PBI-09 — mengelola kategori layanan (portal admin & super admin).
 */
class KategoriController extends Controller
{
    public function index(): View
    {
        return view('kategori.index', [
            'kategori' => KategoriLayanan::withCount('vendor')->orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:80', 'unique:kategori_layanan,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);
        $kategori = KategoriLayanan::create($data);

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function update(Request $request, KategoriLayanan $kategori): RedirectResponse
    {
        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:80', Rule::unique('kategori_layanan', 'nama_kategori')->ignore($kategori->id_kategori, 'id_kategori')],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);
        $kategori->update($data);

        return back()->with('status', 'Kategori diperbarui.');
    }
}
