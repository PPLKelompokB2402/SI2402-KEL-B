@php($portal = request()->routeIs('super-admin.*') ? 'super-admin' : 'admin')
@extends($portal === 'super-admin' ? 'layouts.super-admin' : 'layouts.admin')

@section('title', 'Manajemen kategori')

@section('portal-content')
    <section class="portal-page">
        <div class="admin-page-heading"><div><span class="section-kicker">{{ $portal === 'super-admin' ? 'Super administrator' : 'Admin console' }}</span><h1>Manajemen kategori</h1><p>Kelola kategori yang tersedia di katalog vendor.</p></div></div>
        <section class="portal-panel admin-list-panel">
            <div class="panel-heading"><div><h2>Kategori layanan</h2><p>{{ $kategori->count() }} kategori terdaftar</p></div></div>
            <details class="category-add-details"><summary class="button button-dark">＋ Tambah kategori</summary>
                <form method="POST" action="{{ route($portal.'.kategori.store') }}" class="category-inline-form">@csrf
                    <input name="nama_kategori" required maxlength="80" placeholder="Nama kategori">
                    <input name="deskripsi" maxlength="255" placeholder="Deskripsi (opsional)" class="grow">
                    <button class="button button-dark" type="submit">Simpan</button>
                </form>
            </details>
            <div class="category-admin-rows">
                @forelse($kategori as $item)
                    <details class="category-admin-row"><summary><div><strong>{{ $item->nama_kategori }}</strong><small>{{ $item->vendor_count }} vendor</small></div><span class="button button-outline">Edit</span></summary>
                        <form method="POST" action="{{ route($portal.'.kategori.update', $item) }}" class="category-inline-form">@csrf @method('PATCH')
                            <input name="nama_kategori" required maxlength="80" value="{{ $item->nama_kategori }}">
                            <input name="deskripsi" maxlength="255" value="{{ $item->deskripsi }}" placeholder="Deskripsi (opsional)" class="grow">
                            <button class="button button-dark" type="submit">Simpan perubahan</button>
                        </form>
                    </details>
                @empty
                    <div class="empty-cell">Belum ada kategori.</div>
                @endforelse
            </div>
        </section>
    </section>
@endsection
