# KolaboraEO — Tahap 05: PBI-05 Pencarian & filter

Pelanggan mencari dan menyaring katalog.

## Yang ditambahkan pada tahap ini

- Kata kunci, kategori, lokasi, kisaran harga minimum–maksimum, urutkan berdasarkan harga.

## File yang berubah dari tahap 04

- **baru** `tests/Feature/Us05PencarianFilterTest.php`
- diubah `app/Http/Controllers/CatalogController.php`
- diubah `resources/views/customer/index.blade.php`

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
