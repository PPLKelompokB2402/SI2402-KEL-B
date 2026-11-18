@extends('layouts.admin')

@section('title', 'Verifikasi vendor')

@section('portal-content')
    <section class="portal-page">
        <div class="admin-page-heading"><div><span class="section-kicker">Admin console</span><h1>Verifikasi vendor</h1><p>Pantau pengajuan, periksa dokumen legalitas, lalu perbarui status verifikasi.</p></div><span class="last-updated">Data diperbarui · {{ $updatedAt->format('d M Y, H:i') }}</span></div>
        <section class="portal-panel admin-list-panel">
            <div class="panel-heading"><div><h2>Permintaan verifikasi vendor</h2><p>{{ $vendors->count() }} permintaan menunggu ditinjau</p></div></div>
            <div class="table-scroll"><table class="admin-table verification-table"><thead><tr><th>Nama vendor</th><th>Kategori</th><th>Tanggal daftar</th><th>Aksi</th></tr></thead><tbody>
                @forelse($vendors as $vendor)
                    @php
                        $cek = fn ($jenis) => $jenis->contains('KTP') && $jenis->intersect(['NIB', 'SIUP'])->isNotEmpty();
                        $adaLengkap = $cek($vendor->dokumenLegalitas->where('status_verifikasi', '!=', 'ditolak')->pluck('jenis_dokumen'));
                        $lengkap = $cek($vendor->dokumenLegalitas->where('status_verifikasi', 'valid')->pluck('jenis_dokumen'));
                        $keterangan = $lengkap ? 'Dokumen valid' : ($adaLengkap ? 'Dokumen belum divalidasi' : 'Dokumen belum lengkap');
                        $alasan = $adaLengkap ? 'Tandai dokumen KTP dan NIB/SIUP sebagai Valid terlebih dahulu' : 'Dokumen KTP dan NIB/SIUP belum diunggah atau ada yang ditolak';
                    @endphp
                    <tr><td><strong>{{ $vendor->nama_usaha }}</strong><small>{{ $vendor->user->nama }} · {{ $keterangan }}</small></td><td><span class="category-tag">{{ $vendor->kategori->nama_kategori }}</span></td><td>{{ $vendor->user->created_at->format('d M Y') }}</td><td class="verification-actions">
                        <a class="icon-action" href="{{ route('admin.verifikasi.detail', $vendor) }}" title="Tinjau dokumen">◎</a>
                        <form method="POST" action="{{ route('admin.vendors.status', $vendor) }}">@csrf @method('PATCH')<input type="hidden" name="status_verifikasi" value="ditolak"><button class="reject-button" type="submit">Tolak</button></form>
                        <form method="POST" action="{{ route('admin.vendors.status', $vendor) }}">@csrf @method('PATCH')<input type="hidden" name="status_verifikasi" value="terverifikasi"><button class="button button-dark" type="submit" @unless($lengkap) aria-disabled="true" style="opacity: .5; cursor: not-allowed" title="{{ $alasan }}" @endunless>Verifikasi</button></form>
                    </td></tr>
                @empty
                    <tr><td colspan="4"><div class="empty-cell">Tidak ada pengajuan verifikasi yang menunggu.</div></td></tr>
                @endforelse
            </tbody></table></div>
        </section>
    </section>
@endsection