# KolaboraEO — Tahap 10: PBI-10 Portal login admin terpisah

Login admin dan super admin dipisah dari login pelanggan/vendor.

## Yang ditambahkan pada tahap ini

- URL `/admin/login` dan `/super-admin/login` dengan guard sesi terpisah (`admin`, `super_admin`).
- `/login` kini hanya untuk pelanggan & vendor; peran tidak bisa masuk lewat portal yang salah.

## File yang berubah dari tahap 09

- **baru** `resources/views/auth/admin-login.blade.php`
- **baru** `resources/views/auth/super-admin-login.blade.php`
- **baru** `tests/Feature/Us10PortalAdminTest.php`
- diubah `app/Http/Controllers/AuthController.php`
- diubah `app/Http/Controllers/MediaController.php`
- diubah `bootstrap/app.php`
- diubah `config/auth.php`
- diubah `resources/views/auth/login.blade.php`
- diubah `resources/views/layouts/admin.blade.php`
- diubah `resources/views/layouts/super-admin.blade.php`
- diubah `routes/web.php`
- diubah `tests/Feature/KolaboraTestCase.php`
- diubah `tests/Feature/Us00AutentikasiTest.php`
- diubah `tests/Feature/Us00PerintahAkunTest.php`
- diubah `tests/Feature/Us07TinjauDokumenTest.php`

## Menjalankan

```bash
composer install
cp .env.example .env            # PowerShell: Copy-Item .env.example .env
php artisan key:generate
touch database/database.sqlite  # PowerShell: New-Item -ItemType File database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve

php artisan test                 # pengujian tahap ini
```

Isi `SUPER_ADMIN_NAME`, `SUPER_ADMIN_EMAIL`, `SUPER_ADMIN_PASSWORD` di `.env` sebelum seeding untuk membuat akun Super Admin pertama.

Akun administrator pada tahap ini dibuat dengan `php artisan admin:create-admin "Nama Admin" admin@contoh.com`.
