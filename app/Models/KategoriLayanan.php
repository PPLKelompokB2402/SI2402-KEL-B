<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriLayanan extends Model
{
    protected $table = 'kategori_layanan';

    protected $primaryKey = 'id_kategori';

    public $timestamps = false;

    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function vendor(): HasMany
    {
        return $this->hasMany(Vendor::class, 'id_kategori', 'id_kategori');
    }
}
