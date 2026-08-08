# DoorPrize Draw System (DPDS) — Sprint 1 MVP

Implementasi awal berdasarkan SRS: registrasi peserta via Google OAuth,
generate nomor undian otomatis, dashboard peserta, dashboard panitia,
buka/kunci registrasi, kelola hadiah, dan halaman pengundian fullscreen
dengan animasi + riwayat pemenang + undi ulang + audit log.

## 1. Buat project Laravel 12

```bash
composer create-project laravel/laravel dpds
cd dpds
composer require laravel/socialite
```

## 2. Copy file dari paket ini

Salin seluruh folder/berkas berikut ke dalam project Laravel Anda,
menimpa/menambahkan sesuai path yang sama:

- `app/Models/*.php`
- `app/Http/Controllers/**/*.php`
- `app/Http/Middleware/EnsureCanManageDraw.php`
- `database/migrations/*.php`
- `database/seeders/PromoteAdminSeeder.php`
- `resources/views/**/*.blade.php`
- `routes/web.php` (timpa file default)

Lalu terapkan dua potongan konfigurasi manual:

- `bootstrap-app-middleware-snippet.php` → contoh isi `bootstrap/app.php`
  (tambahkan alias middleware `can.manage.draw`).
- `config-services-snippet.php` → tambahkan blok `google` ke
  `config/services.php`.
- `env-additions.txt` → tambahkan variabel ini ke file `.env` Anda.

## 3. Setup Google OAuth

1. Buka [Google Cloud Console](https://console.cloud.google.com/) → buat
   OAuth Client ID (tipe Web application).
2. Authorized redirect URI: `https://domain-anda.com/auth/google/callback`
   (atau `http://localhost:8000/auth/google/callback` saat development).
3. Isi `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` di `.env`.

## 4. Migrasi database

```bash
php artisan migrate
```

## 5. Jadikan diri Anda admin

1. Jalankan server, buka `/`, klik "Login dengan Google" sekali (akun
   Anda akan otomatis tercatat dengan role `participant`).
2. Edit `database/seeders/PromoteAdminSeeder.php`, ganti email dengan
   email Google Anda.
3. Jalankan:

```bash
php artisan db:seed --class=PromoteAdminSeeder
```

4. Logout lalu login kembali — Anda sekarang otomatis diarahkan ke
   `/admin` (dashboard panitia).

## 6. Alur pemakaian saat acara

1. Panitia login → dashboard panitia menunjukkan status registrasi.
2. Peserta scan QR / buka link → login Google → otomatis mendapat
   nomor undian 4 digit acak & unik.
3. Sebelum acara mulai, panitia klik **Kunci Registrasi** (mencegah
   penambahan/penghapusan peserta agar data final — sesuai saran
   "Registration Lock" di SRS).
4. Panitia tambahkan daftar hadiah di menu **Kelola Hadiah**.
5. Saat sesi doorprize, buka **Pengundian** di layar proyektor (mode
   fullscreen browser: tekan F11), pilih hadiah, klik **START** →
   sistem mengacak peserta yang belum menang dengan `random_int()`
   (bukan `rand()`, sesuai catatan keamanan acak di SRS) dan
   menampilkan pemenang.
6. Jika perlu, panitia bisa klik **Undi Ulang** pada baris riwayat
   untuk membatalkan & mengundi ulang hadiah tersebut.
7. Setiap aksi penting (buka/kunci registrasi, tambah/hapus hadiah,
   undian, undi ulang) otomatis tercatat di tabel `audit_logs`.

## Belum termasuk di MVP ini (Sprint 2–4 pada roadmap SRS)

- Export laporan Excel/PDF
- QR Code generator untuk peserta
- Multi-event
- Tampilan tema per acara
- Live-screen khusus untuk jemaat (terpisah dari layar kontrol panitia)

Beri tahu saya kalau salah satu dari ini ingin dikerjakan berikutnya —
strukturnya (models, migrations, audit log) sudah disiapkan agar mudah
diperluas.
