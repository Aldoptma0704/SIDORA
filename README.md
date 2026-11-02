# SIDORA

Aplikasi web pengelolaan surat dinas berbasis Laravel. SIDORA membantu satu instansi mengelola surat keluar dan surat masuk secara digital: mulai dari penyusunan surat oleh pegawai, persetujuan berjenjang oleh pimpinan, penomoran resmi oleh admin, sampai pembuatan PDF lengkap dengan tanda tangan dan QR code.

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Alur Surat](#alur-surat)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Akun Awal](#akun-awal)
- [Struktur Proyek](#struktur-proyek)
- [Model Data](#model-data)
- [Peta Rute](#peta-rute)
- [Penomoran Surat](#penomoran-surat)
- [Pengujian](#pengujian)
- [Catatan Sebelum Deployment](#catatan-sebelum-deployment)
- [Lisensi](#lisensi)

## Fitur Utama

Aplikasi memiliki tiga peran (role) dengan hak akses terpisah. Pembatasan akses dilakukan oleh `RoleMiddleware` pada setiap grup rute.

**Admin**
- Dashboard ringkasan jumlah pengguna, surat masuk, surat keluar, dan surat yang belum didisposisikan.
- Manajemen pengguna (tambah, ubah, hapus, hapus massal) beserta jabatan, NIP, dan bagian.
- Pencatatan surat masuk dari instansi luar (unggah PDF) dan pengiriman surat tersebut ke pegawai tujuan.
- Laporan surat yang sudah disetujui sepenuhnya, lengkap dengan pembuatan nomor surat resmi.
- Unduh surat dalam bentuk PDF.

**Pegawai**
- Membuat surat keluar dengan editor teks kaya (Quill), dalam dua jenis: `keluar` (kop standar) dan `keluar_full` (kop instansi lengkap dengan logo, alamat, telepon, email, dan website).
- Menentukan satu atau beberapa penandatangan secara berurutan, termasuk tanda tangan sendiri sebagai pembuat.
- Menyimpan draft, mengubah, mengajukan ke pimpinan, memantau status, serta pratinjau dan unduh PDF.
- Mengunggah surat dalam bentuk PDF.

**Pimpinan**
- Menyetujui atau menolak surat (penolakan wajib disertai alasan) dan memberi catatan disposisi.
- Persetujuan berantai: surat diteruskan otomatis ke penandatangan pimpinan berikutnya sampai seluruh rantai selesai.
- Menerima surat masuk, membuat surat balasan, dan melihat status surat balasan.
- Pembatasan khusus: akun Kepala Dinas tidak dapat membuat surat balasan.

**Fitur bersama**
- Profil pengguna: foto profil (dengan fitur potong gambar), tanda tangan digital, dan data jabatan.
- Penyaringan HTML pada isi surat memakai HTMLPurifier untuk mencegah XSS.
- Lencana jumlah surat yang menunggu tindakan pada sidebar tiap peran.
- Zona waktu `Asia/Jakarta` dan bahasa Indonesia sebagai bawaan.

## Alur Surat

Surat keluar:

```
Pegawai membuat draft
        |
        v
Pegawai mengajukan surat           status: draft -> menunggu
        |
        v
Pimpinan 1 menyetujui  ---------> Pimpinan 2 ... Pimpinan N   (sesuai urutan penandatangan)
        |                                   |
        | menolak                           | semua setuju
        v                                   v
status: ditolak                    status: disetujui
(disertai alasan penolakan)        (diteruskan ke Admin)
                                            |
                                            v
                                   Admin membuat nomor surat resmi
                                            |
                                            v
                                   Unduh PDF (tanda tangan + QR code)
```

Surat masuk:

```
Admin mencatat surat masuk (unggah PDF)
        |
        v
Admin mengirim ke pegawai tujuan   status: dikirim
```

Status surat yang dipakai aplikasi: `draft_pimpinan`, `draft`, `menunggu`, `disetujui`, `ditolak`, `dikirim`.

## Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Bahasa dan framework | PHP 8.1 atau lebih baru, Laravel 10 |
| Basis data | MySQL |
| Autentikasi | Session Laravel dengan RBAC kustom (`RoleMiddleware`), Laravel Sanctum |
| Antarmuka | Blade, Tailwind CSS (CDN), Alpine.js (CDN), Font Awesome |
| Editor teks | Quill 2 |
| Pemotong gambar | Cropper.js |
| Pembuat PDF | barryvdh/laravel-dompdf |
| QR code | simplesoftwareio/simple-qrcode |
| Sanitasi HTML | mews/purifier (HTMLPurifier) |
| Build aset | Vite 4 dengan laravel-vite-plugin |
| Pengujian | PHPUnit 10 |

## Persyaratan Sistem

- PHP 8.1 atau lebih baru dengan ekstensi umum Laravel (`mbstring`, `openssl`, `pdo_mysql`, `fileinfo`, `gd`, `xml`, `ctype`, `json`)
- Composer 2
- Node.js 16 atau lebih baru beserta npm
- MySQL atau MariaDB
- Koneksi internet saat dijalankan, karena Tailwind, Alpine, Quill, Cropper.js, dan Font Awesome dimuat dari CDN

## Instalasi

```bash
# 1. Unduh proyek
git clone <url-repositori> sidora
cd sidora

# 2. Pasang dependensi
composer install
npm install

# 3. Siapkan konfigurasi
cp .env.example .env
php artisan key:generate
```

Buat database kosong bernama `sidora`, lalu sesuaikan bagian berikut pada berkas `.env`:

```env
APP_NAME=SIDORA
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sidora
DB_USERNAME=root
DB_PASSWORD=
```

Lanjutkan dengan migrasi, seeder, dan menjalankan aplikasi:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build

php artisan serve
```

Aplikasi dapat dibuka di `http://localhost:8000`. Selama pengembangan antarmuka, jalankan `npm run dev` pada terminal terpisah.

Pada lingkungan Windows (misalnya Laragon), symlink `storage:link` kadang bermasalah. Untuk itu aplikasi sudah menyediakan rute `/storage/{path}` yang melayani berkas dari `storage/app/public` secara langsung.

## Akun Awal

`UserSeeder` membuat tiga akun contoh:

| Peran | Email | Kata sandi |
| --- | --- | --- |
| Admin | `admin@example.com` | `admin123` |
| Pegawai | `pegawai@example.com` | `pegawai123` |
| Pimpinan | `pimpinan@example.com` | `pimpinan123` |

Akun ini hanya untuk pengembangan. Ganti kata sandinya atau hapus akun tersebut sebelum aplikasi dipakai secara nyata.

Setelah login sebagai admin, buka menu Pengguna dan lengkapi **jabatan (position)** serta **bagian** untuk setiap akun pimpinan. Aplikasi mencocokkan penandatangan dengan akun pimpinan berdasarkan kedua kolom tersebut, dan akun dikenali sebagai Kepala Dinas jika jabatan atau bagiannya memuat kata "kepala dinas" atau "kadis".

## Struktur Proyek

```
app/
  Http/
    Controllers/
      Admin/         UserController, SuratController (surat masuk, laporan, penomoran, PDF)
      Pegawai/       SuratController, PegawaiDashboardController
      Pimpinan/      SuratController (persetujuan, disposisi, surat balasan)
      AuthController, ProfileController
    Middleware/      RoleMiddleware (RBAC)
  Models/            User, Surat, SuratPenandatangan, Disposisi
  Providers/         AppServiceProvider (locale, zona waktu, data lencana sidebar)
database/
  migrations/        users, surats, disposis, surat_penandatangan, dan penambahan kolom
  seeders/           UserSeeder, DatabaseSeeder
resources/
  views/
    admin/ pegawai/ pimpinan/ auth/ profile/
    layouts/         app.blade.php dan partials (header, sidebar per peran, footer)
    components/      card, page-header, stat-card, status-badge
routes/              web.php, api.php
storage/app/public/  logos, profile_photos, signatures, surat_files, surat_masuk
tests/Feature/       loginTest.php
```

## Model Data

| Tabel | Fungsi | Kolom penting |
| --- | --- | --- |
| `users` | Akun pengguna | `name`, `email`, `role` (admin, pegawai, pimpinan), `position`, `nip`, `bagian`, `signature`, `profile_photo` |
| `surats` | Surat masuk dan keluar | `user_id`, `pengirim_id`, `judul`, `nomor_surat`, `jenis` (masuk, keluar, keluar_full), `status`, `isi`, `tujuan`, data kop instansi, `disposisi_user_id`, `perlu_disposisi_admin`, `alasan_penolakan`, `file_path` |
| `surat_penandatangan` | Rantai penandatangan tiap surat | `surat_id`, `user_id`, `peran` (pembuat, pimpinan), `urutan`, `jabatan`, `nama`, `nip`, `status` (menunggu, ditandatangani, ditolak), `tanda_tangan`, `signed_at` |
| `disposisis` | Catatan disposisi | `surat_id`, `pimpinan_id`, `catatan` |

Relasi utama: satu `User` memiliki banyak `Surat`; satu `Surat` memiliki banyak `SuratPenandatangan` yang diurutkan menurut kolom `urutan`.

## Peta Rute

| Prefix | Peran | Fungsi utama |
| --- | --- | --- |
| `/login`, `/logout`, `/reset-password` | Publik | Autentikasi dan penggantian kata sandi |
| `/admin/*` | admin | Dashboard, pengguna, laporan surat, surat masuk, penomoran, unduh PDF |
| `/pegawai/*` | pegawai | Dashboard, buat, ubah, ajukan, status, pratinjau, unduh, unggah PDF |
| `/pimpinan/*` | pimpinan | Dashboard, setujui, tolak, disposisi, surat balasan, surat masuk |
| `/profile` | semua peran | Ubah profil, foto, dan tanda tangan |
| `/storage/{path}` | Publik | Melayani berkas `storage/app/public` |

Setelah login, pengguna diarahkan ke `/{role}/dashboard`. Daftar lengkap dapat dilihat dengan `php artisan route:list`.

## Penomoran Surat

Nomor resmi dibuat oleh admin pada halaman laporan setelah surat berstatus `disetujui`. Formatnya:

```
{kode_klasifikasi}/{nomor_urut}/{kode_unit}/{bulan}/{tahun}
```

Contoh: `500.15.6.1/0001/DISNAKER/10/2026`

- `nomor_urut` disarankan otomatis oleh sistem berdasarkan nomor tertinggi pada tahun berjalan, lalu diisi ulang menjadi minimal empat digit.
- `bulan` diisi dua digit.
- Daftar kode klasifikasi arsip (misalnya `500.15.x` untuk ketenagakerjaan, `800` untuk kepegawaian, `000` untuk umum) didefinisikan pada `Admin\SuratController::getKlasifikasiArsip()` dan dapat disesuaikan dengan kebutuhan instansi.

## Pengujian

```bash
php artisan test
```

Saat ini tersedia pengujian fitur untuk alur login (kredensial benar dan salah) pada `tests/Feature/loginTest.php`.

## Catatan Sebelum Deployment

Lakukan langkah berikut saat memindahkan aplikasi ke server produksi:

1. Atur `APP_ENV=production` dan `APP_DEBUG=false` pada `.env`.
2. Ganti seluruh kata sandi akun contoh dan pastikan `.env` tidak ikut terunggah ke repositori.
3. Arahkan document root web server ke folder `public`.
4. Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis oleh web server.
5. Jalankan `php artisan config:cache`, `php artisan route:cache`, dan `php artisan view:cache`.
6. Gunakan HTTPS dan batasi akses ke folder `storage/app/public/surat_files` serta `surat_masuk` karena berisi dokumen resmi.

## Lisensi

Proyek ini dirilis dengan lisensi [MIT](https://opensource.org/licenses/MIT), mengikuti lisensi kerangka kerja Laravel.