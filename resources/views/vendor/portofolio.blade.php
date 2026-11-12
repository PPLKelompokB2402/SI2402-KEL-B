@extends('layouts.portal')

@section('title', 'Portofolio')

@section('portal-content')
    <section class="portal-page">
        <div class="portal-heading"><div><span class="section-kicker">Karya terbaikmu</span><h1>Portofolio</h1><p>Dokumentasikan hasil kerja yang dapat dilihat calon pelanggan. Watermark diterapkan otomatis.</p></div></div>
        @forelse($vendors as $vendor)
            <section class="portal-panel portfolio-manage-panel">
                <div class="panel-heading"><div><h2>{{ $vendor->nama_usaha }}</h2><p>{{ $vendor->portofolio->count() }} item portofolio</p></div><span class="status-tag {{ $vendor->status_verifikasi }}">{{ $vendor->status_label }}</span></div>
                <form method="POST" action="{{ route('vendor.portofolio.simpan', $vendor) }}" enctype="multipart/form-data" class="portfolio-manage-form four">@csrf
                    <label>Judul karya<input name="judul_proyek" required maxlength="120" placeholder="Contoh: Dekorasi intimate wedding"></label>
                    <label>Deskripsi (opsional)<input name="deskripsi_proyek" maxlength="500"></label>
                    <label>Foto<input name="media" type="file" required accept=".jpg,.jpeg,.png,.webp"></label>
                    <button class="button button-dark" type="submit">＋ Tambah karya</button>
                </form>
                <div class="managed-portfolio-grid">
                    @forelse($vendor->portofolio as $item)
                        <article><div class="managed-portfolio-cover" style="--cover: url('{{ route('media.portofolio', $item) }}')"></div><div class="managed-portfolio-meta"><strong>{{ $item->judul_proyek }}</strong><form method="POST" action="{{ route('vendor.portofolio.hapus', $item) }}" onsubmit="return confirm('Hapus portofolio ini?')">@csrf @method('DELETE')<button class="delete-icon" type="submit" aria-label="Hapus portofolio">×</button></form></div></article>
                    @empty
                        <p class="muted">Belum ada karya. Tambahkan portofolio pertama di atas.</p>
                    @endforelse
                </div>
            </section>
        @empty
            <div class="empty-state"><div>▧</div><h3>Tambahkan usaha terlebih dahulu</h3><p>Portofolio akan dikelompokkan berdasarkan profil usaha.</p><a class="button button-dark" href="{{ route('vendor.usaha') }}">Kelola usaha</a></div>
        @endforelse
    </section>
@endsection
