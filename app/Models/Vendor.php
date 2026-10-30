<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vendor extends Model
{
    public const MENUNGGU = 'menunggu';
    public const TERVERIFIKASI = 'terverifikasi';
    public const DITOLAK = 'ditolak';

    public const STATUS_LABEL = [
        self::MENUNGGU => 'Menunggu verifikasi',
        self::TERVERIFIKASI => 'Terverifikasi',
        self::DITOLAK => 'Ditolak',
    ];

    protected $table = 'vendor';

    protected $primaryKey = 'id_vendor';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_kategori',
        'nama_usaha',
        'deskripsi_usaha',
        'lokasi_layanan',
        'kisaran_harga',
        'status_verifikasi',
    ];

    protected function casts(): array
    {
        return ['kisaran_harga' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriLayanan::class, 'id_kategori', 'id_kategori');
    }

    public function scopeTerverifikasi(Builder $query): Builder
    {
        return $query->where('status_verifikasi', self::TERVERIFIKASI);
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp'.number_format((float) $this->kisaran_harga, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status_verifikasi] ?? $this->status_verifikasi;
    }

    public function getSampulUrlAttribute(): ?string
    {
        return null;
    }

    /** Link WhatsApp dari no_hp akun vendor agar pelanggan dapat menghubungi langsung. */
    public function getKontakUrlAttribute(): ?string
    {
        $nomor = preg_replace('/\D+/', '', (string) $this->user?->no_hp);
        if ($nomor === '') {
            return null;
        }
        if (str_starts_with($nomor, '0')) {
            $nomor = '62'.substr($nomor, 1);
        }

        return 'https://wa.me/'.$nomor;
    }
}
