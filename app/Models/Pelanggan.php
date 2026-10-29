<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'id_pelanggan';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id_pelanggan', 'alamat', 'foto_profile'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pelanggan', 'id_user');
    }

}
