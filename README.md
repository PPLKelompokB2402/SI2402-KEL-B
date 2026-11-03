# KolaboraEO — Tahap 02: PBI-02 Unggah dokumen legalitas

Vendor mengunggah KTP dan NIB/SIUP untuk verifikasi.

## Yang ditambahkan pada tahap ini

- Form unggah di halaman "Profil & legalitas"; PDF/JPG/PNG maks. 4 MB, unggah ulang mengganti berkas lama.
- Berkas disimpan privat (`storage/app/private`) dan hanya bisa dibuka pemilik/petugas lewat `/media/dokumen/{id}`.
- Tabel baru: `dokumen_legalitas`.

## File yang berubah dari tahap 01

- **baru** `app/Http/Controllers/MediaController.php`
- **baru** `app/Models/DokumenLegalitas.php`
- **baru** `database/migrations/2026_10_23_000006_create_dokumen_legalitas_table.php`
- **baru** `tests/Feature/Us02LegalitasTest.php`
- diubah `app/Http/Controllers/VendorPortalController.php`
- diubah `app/Models/Administrator.php`
- diubah `app/Models/Vendor.php`
- diubah `resources/views/vendor/profil.blade.php`
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
