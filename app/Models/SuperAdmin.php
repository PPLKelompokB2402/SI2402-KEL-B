<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuperAdmin extends Model
{
    protected $table = 'super_admin';

    protected $primaryKey = 'id_super_admin';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id_super_admin', 'hak_akses'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_super_admin', 'id_user');
    }

    public function administrator(): HasMany
    {
        return $this->hasMany(Administrator::class, 'id_super_admin', 'id_super_admin');
    }
}
