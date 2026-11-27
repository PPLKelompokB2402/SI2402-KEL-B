# KolaboraEO — Tahap 11a: US-11 bagian 1: Backend kelola akun administrator

Logika server untuk membuat, mengedit, menonaktifkan/mengaktifkan, dan menghapus akun administrator. Belum ada halaman; diuji lewat tes otomatis.

## Yang ditambahkan pada tahap ini

- `AdministratorController` + route di grup portal admin (`auth:admin`, `role:admin`):
  - `POST /admin/administrator` — membuat akun admin (nama, email unik, kata sandi min. 8 + konfirmasi); akun langsung aktif (AC-01).
  - `PUT /admin/administrator/{user}` — mengedit nama, email, dan kata sandi (opsional; kosong = tidak diganti).
  - `PATCH /admin/administrator/{user}/status` — menonaktifkan/mengaktifkan kembali admin lain (AC-02).
  - `DELETE /admin/administrator/{user}` — menghapus akun admin lain.
- AC-05: admin tidak bisa menonaktifkan/menghapus akunnya sendiri dan admin aktif terakhir tidak bisa dihilangkan.
- AC-03/AC-04: semua admin aktif punya akses setara; admin nonaktif tidak bisa login dan sesinya ditolak, admin lain tetap bisa mengerjakan antrean verifikasi.
- AC-06: registrasi publik tetap hanya menerima peran pelanggan/vendor.
- Tidak ada tabel baru (memakai `users` dan `administrator`).

## File yang berubah dari tahap 10

- **baru** `app/Http/Controllers/AdministratorController.php`
- **baru** `tests/Feature/Us11aKelolaAdministratorBackendTest.php`
- diubah `routes/web.php`

## Menjalankan

Melanjutkan database yang sudah dipakai di tahap sebelumnya (XAMPP/MySQL):

```bash
composer install
php artisan migrate        # tidak ada migrasi baru di tahap ini
php artisan serve

php artisan test           # pengujian otomatis
```

Database baru dari nol: `php artisan migrate:fresh --seed`. Akun bawaan: `admin@kolabora.test` / `12345678` (login di `/admin/login`).
