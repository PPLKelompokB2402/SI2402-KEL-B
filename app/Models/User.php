<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Entitas User — superclass seluruh pengguna (Pelanggan, Vendor, Admin, SuperAdmin).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    public const ROLE_PELANGGAN = 'pelanggan';
    public const ROLE_VENDOR = 'vendor';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const UPDATED_AT = null;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    /** Kolom kata sandi pada proposal bernama password_hash. */
    protected $authPasswordName = 'password_hash';

    /** Tabel users tidak memiliki remember_token (tidak ada di ERD). */
    protected $rememberTokenName = '';

    protected $fillable = ['nama', 'email', 'password_hash', 'no_hp', 'role', 'status_aktif'];

    protected $hidden = ['password_hash'];

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'status_aktif' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class, 'id_pelanggan', 'id_user');
    }

    /** Profil usaha milik akun vendor (satu akun dapat memiliki beberapa usaha). */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class, 'id_user', 'id_user');
    }

    public function administrator(): HasOne
    {
        return $this->hasOne(Administrator::class, 'id_admin', 'id_user');
    }

    public function superAdmin(): HasOne
    {
        return $this->hasOne(SuperAdmin::class, 'id_super_admin', 'id_user');
    }

    public function getInisialAttribute(): string
    {
        return collect(explode(' ', trim($this->nama)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function getRoleLabelAttribute(): string
    {
        return [
            self::ROLE_PELANGGAN => 'Pelanggan',
            self::ROLE_VENDOR => 'Vendor',
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_SUPER_ADMIN => 'Super Admin',
        ][$this->role] ?? $this->role;
    }
}
