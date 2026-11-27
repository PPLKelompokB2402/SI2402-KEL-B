# KolaboraEO — Tahap 11b: US-11 bagian 2: Halaman membuat akun administrator

Tampilan daftar administrator dan form pembuatan akun.

## Yang ditambahkan pada tahap ini

- Menu **Administrator** di portal super admin (`/super-admin/administrator`) dan pintasan di Ringkasan super admin.
- Daftar akun administrator beserta status Aktif/Nonaktif dan jumlah admin aktif.
- Tombol **＋ Buat akun administrator** membuka form nama, email, kata sandi awal, ulangi kata sandi; bila ada kesalahan form tetap terbuka dengan isian lama.

## File yang berubah dari tahap 11a

- **baru** `resources/views/super-admin/administrator.blade.php`
- **baru** `tests/Feature/Us11bMembuatAdministratorTest.php`
- diubah `app/Http/Controllers/AdministratorController.php`
- diubah `public/css/app.css`
- diubah `resources/views/layouts/super-admin.blade.php`
- diubah `resources/views/super-admin/dashboard.blade.php`
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
