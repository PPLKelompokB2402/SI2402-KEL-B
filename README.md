# KolaboraEO — Tahap 00: Login & register dengan session

Fondasi autentikasi sebelum user story dimulai.

## Yang ditambahkan pada tahap ini

- Registrasi akun pelanggan dan vendor (`/register`), password di-hash ke kolom `password_hash`.
- Login & logout (`/login`, `/logout`); sesi disimpan di tabel `sessions` (`SESSION_DRIVER=database`) dan di-regenerate saat login.
- Middleware `role:` membatasi halaman per peran; akun nonaktif tidak bisa masuk.
- Pada tahap ini admin & super admin masih masuk lewat `/login` yang sama (dipisah di US-10).
- Akun Super Admin pertama dibuat dari seeder (`SUPER_ADMIN_*` di `.env`) atau `php artisan admin:create-super-admin`. Akun admin sementara dibuat dengan `php artisan admin:create-admin` (digantikan portal super admin di US-12).
- Tabel: `users`, `pelanggan`, `administrator`, `super_admin`, `sessions` (+ `cache`, `jobs` bawaan Laravel).

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

Admin dan super admin masuk lewat `/login` yang sama dengan pelanggan/vendor.
