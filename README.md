# KolaboraEO — Tahap 11d: US-11 bagian 4: Menonaktifkan dan menghapus akun administrator

Tombol untuk menonaktifkan/mengaktifkan kembali dan menghapus akun administrator.

## Yang ditambahkan pada tahap ini

- Tombol **Nonaktifkan / Aktifkan** dan **Hapus** (dengan konfirmasi) untuk setiap akun admin.
- Pesan kesalahan tampil bila super admin mencoba menonaktifkan/menghapus admin aktif terakhir.
- AC-07: pencatatan ke audit log ditambahkan pada tahap 16 (US-16).

## File yang berubah dari tahap 11c

- **baru** `tests/Feature/Us11dNonaktifHapusAdministratorTest.php`
- diubah `resources/views/super-admin/administrator.blade.php`

## Menjalankan

Melanjutkan database yang sudah dipakai di tahap sebelumnya (XAMPP/MySQL):

```bash
composer install
php artisan migrate        # tidak ada migrasi baru di tahap ini
php artisan serve

php artisan test           # pengujian otomatis
```

Database baru dari nol: `php artisan migrate:fresh --seed`. Akun bawaan (kata sandi `12345678`): super admin `superadmin@kolabora.test` (login di `/super-admin/login`) dan admin `admin@kolabora.test` (login di `/admin/login`).
