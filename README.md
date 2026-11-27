# KolaboraEO — Tahap 11c: US-11 bagian 3: Halaman mengedit akun administrator

Tampilan untuk mengedit akun administrator.

## Yang ditambahkan pada tahap ini

- Tombol **Edit** pada setiap baris daftar administrator.
- Halaman `/super-admin/administrator/{id}/edit`: ubah nama, email, dan kata sandi baru (kosongkan bila tidak diganti).
- Setelah disimpan kembali ke daftar dengan pesan berhasil; bila ada kesalahan kembali ke form edit.

## File yang berubah dari tahap 11b

- **baru** `resources/views/super-admin/administrator-edit.blade.php`
- **baru** `tests/Feature/Us11cMengeditAdministratorTest.php`
- diubah `app/Http/Controllers/AdministratorController.php`
- diubah `public/css/app.css`
- diubah `resources/views/super-admin/administrator.blade.php`
- diubah `routes/web.php`

## Menjalankan

Melanjutkan database yang sudah dipakai di tahap sebelumnya (XAMPP/MySQL):

```bash
composer install
php artisan migrate        # tidak ada migrasi baru di tahap ini
php artisan serve

php artisan test           # pengujian otomatis
```

Database baru dari nol: `php artisan migrate:fresh --seed`. Akun bawaan (kata sandi `12345678`): super admin `superadmin@kolabora.test` (login di `/super-admin/login`) dan admin `admin@kolabora.test` (login di `/admin/login`).
