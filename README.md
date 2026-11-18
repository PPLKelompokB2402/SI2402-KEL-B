# KolaboraEO — Tahap 08a: PBI-08 Status verifikasi vendor (backend)

Logika verifikasi dan penolakan vendor di sisi server. Tombolnya di halaman admin baru ditambahkan di tahap 08b.

## Yang ditambahkan pada tahap ini

- Route `PATCH /admin/vendors/{vendor}/status` (nama `admin.vendors.status`) yang ditangani `AdminController::ubahStatusVendor`.
- Validasi nilai status: `menunggu`, `terverifikasi`, atau `ditolak`.
- Vendor hanya bisa diverifikasi jika KTP dan NIB/SIUP sudah diunggah dan tidak ada yang ditolak (method `dokumenLengkap`); bila belum, permintaan ditolak dengan pesan error.
- Saat vendor diverifikasi, dokumen yang masih menunggu ikut dinyatakan valid oleh admin yang memverifikasi, dalam satu transaksi database.
- Pengujian backend di `tests/Feature/Us08StatusVerifikasiTest.php`.
- Belum ada tombol di tampilan. Endpoint dicoba lewat `php artisan test --filter=Us08`.

## File yang berubah dari tahap 07

- **baru** `tests/Feature/Us08StatusVerifikasiTest.php`
- diubah `app/Http/Controllers/AdminController.php`
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
