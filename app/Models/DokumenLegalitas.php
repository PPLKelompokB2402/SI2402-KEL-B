<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenLegalitas extends Model
{
    public const JENIS = ['KTP', 'NIB', 'SIUP'];

    public const MENUNGGU = 'menunggu';
    public const VALID = 'valid';
    public const DITOLAK = 'ditolak';

    public const STATUS_LABEL = [
        self::MENUNGGU => 'Menunggu pemeriksaan',
        self::VALID => 'Valid',
        self::DITOLAK => 'Ditolak',
    ];

    protected $table = 'dokumen_legalitas';

    protected $primaryKey = 'id_legalitas';

    public $timestamps = false;

    protected $fillable = [
        'id_vendor',
        'id_admin',
        'jenis_dokumen',
        'no_dokumen',
        'media_url',
        'status_verifikasi',
        'catatan_admin',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'id_admin', 'id_admin');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status_verifikasi] ?? $this->status_verifikasi;
    }
}
