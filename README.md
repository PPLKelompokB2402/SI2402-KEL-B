# KolaboraEO — Tahap 08b: PBI-08 Status verifikasi vendor (frontend)

Tombol Tolak dan Verifikasi di halaman admin, memakai endpoint dari tahap 08a.

## Yang ditambahkan pada tahap ini

- Tombol **Tolak** dan **Verifikasi** di setiap baris tabel `/admin/verifikasi`. Keduanya mengirim `PATCH` ke `admin.vendors.status`.
- Tombol Verifikasi nonaktif, dengan keterangan, bila dokumen KTP dan NIB/SIUP belum lengkap.
- Hasilnya tampil lewat pesan flash yang sudah ada; vendor yang terverifikasi langsung muncul di katalog.

## File yang berubah dari tahap 08a

- diubah `resources/views/admin/verifikasi.blade.php`

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
