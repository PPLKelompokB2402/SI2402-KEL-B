# KolaboraEO — Tahap 01: PBI-01 Profil usaha vendor

Vendor membuat dan mengelola profil usaha; satu akun bisa memiliki beberapa usaha.

## Yang ditambahkan pada tahap ini

- Halaman "Usaha saya" (`/vendor/usaha`) untuk menambah usaha, dan halaman profil (`/vendor/profil?vendor=`) untuk mengubahnya.
- Profil baru/diubah berstatus *menunggu verifikasi*. Hanya pemilik yang dapat mengubah usahanya.
- Dashboard vendor menampilkan kelengkapan profil.
- Tabel baru: `kategori_layanan` (seeder mengisi 6 kategori), `vendor`.

## File yang berubah dari tahap 00

- **baru** `app/Models/KategoriLayanan.php`
- **baru** `app/Models/Vendor.php`
- **baru** `database/migrations/2026_10_23_000004_create_kategori_layanan_table.php`
- **baru** `database/migrations/2026_10_23_000005_create_vendor_table.php`
- **baru** `resources/views/vendor/partials/form-profil.blade.php`
- **baru** `resources/views/vendor/profil.blade.php`
- **baru** `resources/views/vendor/usaha.blade.php`
- **baru** `tests/Feature/Us01ProfilUsahaTest.php`
- diubah `app/Http/Controllers/AuthController.php`
- diubah `app/Http/Controllers/VendorPortalController.php`
- diubah `app/Models/User.php`
- diubah `database/seeders/DatabaseSeeder.php`
- diubah `resources/views/layouts/portal.blade.php`
- diubah `resources/views/vendor/dashboard.blade.php`
- diubah `routes/web.php`
- diubah `tests/Feature/HalamanDapatDirenderTest.php`
- diubah `tests/Feature/KolaboraTestCase.php`
- diubah `tests/Feature/Us00AutentikasiTest.php`

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
