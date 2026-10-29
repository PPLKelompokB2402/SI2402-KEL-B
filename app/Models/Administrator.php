<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrator extends Model
{
    protected $table = 'administrator';

    protected $primaryKey = 'id_admin';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['id_admin', 'level_admin', 'id_super_admin'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin', 'id_user');
    }

    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, 'id_super_admin', 'id_super_admin');
    }

}
