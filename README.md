# KolaboraEO — Tahap 04: PBI-04 Katalog vendor terverifikasi

Beranda menjadi katalog yang hanya menampilkan vendor terverifikasi.

## Yang ditambahkan pada tahap ini

- Halaman `/` menampilkan kartu vendor terverifikasi (6 per halaman).
- Catatan: tombol verifikasi admin baru ada di US-08. Untuk mencoba katalog di tahap 04–07, ubah status lewat tinker: `php artisan tinker` lalu `App\Models\Vendor::query()->update(['status_verifikasi' => 'terverifikasi'])`.

## File yang berubah dari tahap 03

- **baru** `app/Http/Controllers/CatalogController.php`
- **baru** `resources/views/customer/index.blade.php`
- **baru** `tests/Feature/Us04KatalogTest.php`
- diubah `routes/web.php`
- dihapus `resources/views/home.blade.php`

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
