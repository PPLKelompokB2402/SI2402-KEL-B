# KolaboraEO — Tahap 06: PBI-06 Detail vendor

Halaman detail vendor untuk pelanggan.

## Yang ditambahkan pada tahap ini

- Tab Ringkasan, Galeri Portofolio, Harga & Detail; tombol hubungi vendor via WhatsApp.
- Vendor yang belum terverifikasi menghasilkan 404.

## File yang berubah dari tahap 05

- **baru** `resources/views/customer/harga.blade.php`
- **baru** `resources/views/customer/partials/detail-header.blade.php`
- **baru** `resources/views/customer/portofolio.blade.php`
- **baru** `resources/views/customer/show.blade.php`
- **baru** `tests/Feature/Us06DetailVendorTest.php`
- diubah `app/Http/Controllers/CatalogController.php`
- diubah `resources/views/customer/index.blade.php`
- diubah `resources/views/vendor/dashboard.blade.php`
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
