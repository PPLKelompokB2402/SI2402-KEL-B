# KolaboraEO — Tahap 09: PBI-09 Kelola kategori

Admin dan super admin mengelola kategori layanan.

## Yang ditambahkan pada tahap ini

- Tambah & ubah kategori di `/admin/kategori` dan `/super-admin/kategori`; nama kategori unik.

## File yang berubah dari tahap 08

- **baru** `app/Http/Controllers/KategoriController.php`
- **baru** `resources/views/kategori/index.blade.php`
- **baru** `tests/Feature/Us09KategoriTest.php`
- diubah `resources/views/admin/dashboard.blade.php`
- diubah `resources/views/layouts/admin.blade.php`
- diubah `resources/views/layouts/super-admin.blade.php`
- diubah `resources/views/super-admin/dashboard.blade.php`
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
