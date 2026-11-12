<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portofolio extends Model
{
    public const CREATED_AT = 'tanggal_unggah';
    public const UPDATED_AT = null;

    protected $table = 'portofolio';

    protected $primaryKey = 'id_portofolio';

    protected $fillable = [
        'id_vendor',
        'judul_proyek',
        'deskripsi_proyek',
        'media_url',
        'media_watermark_url',
    ];

    protected function casts(): array
    {
        return ['tanggal_unggah' => 'datetime'];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }
}
