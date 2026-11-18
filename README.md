# KolaboraEO — Tahap 07: PBI-07 Admin meninjau dokumen

Administrator memeriksa dokumen legalitas vendor.

## Yang ditambahkan pada tahap ini

- Antrean verifikasi `/admin/verifikasi` dan detail `/admin/verifikasi/{vendor}`.
- Dokumen dinyatakan valid/ditolak; penolakan wajib disertai catatan yang tampil di halaman vendor.

## File yang berubah dari tahap 06

- **baru** `resources/views/admin/verifikasi-detail.blade.php`
- **baru** `resources/views/admin/verifikasi.blade.php`
- **baru** `tests/Feature/Us07TinjauDokumenTest.php`
- diubah `app/Http/Controllers/AdminController.php`
- diubah `resources/views/admin/dashboard.blade.php`
- diubah `resources/views/layouts/admin.blade.php`
- diubah `routes/web.php`

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
