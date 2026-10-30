{{-- Field profil usaha (dipakai di form tambah & ubah). $item = Vendor|null --}}
<label>Nama usaha<input name="nama_usaha" value="{{ old('nama_usaha', $item?->nama_usaha) }}" required maxlength="150"></label>
<label>Kategori utama<select name="id_kategori" required><option value="">Pilih kategori</option>@foreach($kategori as $k)<option value="{{ $k->id_kategori }}" @selected((string) old('id_kategori', $item?->id_kategori) === (string) $k->id_kategori)>{{ $k->nama_kategori }}</option>@endforeach</select></label>
<label>Lokasi<input name="lokasi_layanan" value="{{ old('lokasi_layanan', $item?->lokasi_layanan) }}" required maxlength="150"></label>
<label>Kisaran harga mulai (Rp)<input name="kisaran_harga" type="number" min="0" step="1000" value="{{ old('kisaran_harga', $item ? (int) $item->kisaran_harga : '') }}" required></label>
<label class="{{ $span ?? '' }}">Deskripsi layanan<textarea name="deskripsi_usaha" rows="{{ $rows ?? 5 }}" required maxlength="3000">{{ old('deskripsi_usaha', $item?->deskripsi_usaha) }}</textarea></label>
<label>WhatsApp<input name="no_hp" type="tel" value="{{ old('no_hp', auth('web')->user()->no_hp) }}" required maxlength="20" placeholder="08xxxxxxxxxx"></label>
