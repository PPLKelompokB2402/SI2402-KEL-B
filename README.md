# KolaboraEO — Tahap 11a: US-11 bagian 1: Backend kelola akun administrator

Logika server agar **super admin** dapat membuat, mengedit, menonaktifkan/mengaktifkan, dan menghapus akun administrator. Belum ada halaman; diuji lewat tes otomatis.

## Yang ditambahkan pada tahap ini

- `AdministratorController` + route di grup portal super admin (`auth:super_admin`, `role:super_admin`); admin biasa, vendor, pelanggan, dan tamu tidak punya akses:
  - `POST /super-admin/administrator` — membuat akun admin (nama, email unik, kata sandi min. 8 + konfirmasi); akun langsung aktif (AC-01).
  - `PUT /super-admin/administrator/{user}` — mengedit nama, email, dan kata sandi (opsional; kosong = tidak diganti).
  - `PATCH /super-admin/administrator/{user}/status` — menonaktifkan/mengaktifkan kembali admin (AC-02).
  - `DELETE /super-admin/administrator/{user}` — menghapus akun admin.
- AC-05: admin aktif terakhir tidak bisa dinonaktifkan atau dihapus.
- AC-03/AC-04: semua admin aktif punya akses setara di portal admin; admin nonaktif tidak bisa login dan sesinya ditolak, admin lain tetap bisa mengerjakan antrean verifikasi.
- AC-06: registrasi publik tetap hanya menerima peran pelanggan/vendor.
- Tidak ada tabel baru (memakai `users` dan `administrator`).

## File yang berubah dari tahap 10

- **baru** `app/Http/Controllers/AdministratorController.php`
- **baru** `tests/Feature/Us11aKelolaAdministratorBackendTest.php`
- diubah `app/Http/Controllers/SuperAdminController.php`
- diubah `routes/web.php`

## Menjalankan

Melanjutkan database yang sudah dipakai di tahap sebelumnya (XAMPP/MySQL):

```bash
composer install
php artisan migrate        # tidak ada migrasi baru di tahap ini
php artisan serve

php artisan test           # pengujian otomatis
```

Database baru dari nol: `php artisan migrate:fresh --seed`. Akun bawaan (kata sandi `12345678`): super admin `superadmin@kolabora.test` (login di `/super-admin/login`) dan admin `admin@kolabora.test` (login di `/admin/login`).
