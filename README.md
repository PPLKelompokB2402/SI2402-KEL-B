# KolaboraEO — Tahap 03: PBI-03 Portofolio dengan watermark

Vendor menambah/menghapus portofolio; watermark dibuat otomatis.

## Yang ditambahkan pada tahap ini

- Halaman `/vendor/portofolio` per usaha; gambar JPG/PNG/WEBP maks. 5 MB.
- File asli disimpan privat, versi ber-watermark (ekstensi GD) disajikan publik lewat `/media/portofolio/{id}`.
- Tabel baru: `portofolio`.

## File yang berubah dari tahap 02

- **baru** `app/Models/Portofolio.php`
- **baru** `app/Services/WatermarkService.php`
- **baru** `database/migrations/2026_10_23_000007_create_portofolio_table.php`
- **baru** `resources/views/vendor/portofolio.blade.php`
- **baru** `tests/Feature/Us03PortofolioTest.php`
- diubah `app/Http/Controllers/MediaController.php`
- diubah `app/Http/Controllers/VendorPortalController.php`
- diubah `app/Models/Vendor.php`
- diubah `resources/views/layouts/portal.blade.php`
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
