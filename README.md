Inventory Management System
Tentang Project
Project ini adalah aplikasi web Inventory Management System yang dibuat untuk membantu pengelolaan data barang atau stok gudang. Aplikasi ini dibuat menggunakan Laravel dan ditujukan sebagai project pembelajaran/mini project.

Dengan aplikasi ini, pengguna bisa melihat data persediaan, mencatat barang masuk dan barang keluar, serta melihat laporan stok. Hak akses dibagi menjadi dua, yaitu Admin dan Staff, supaya menu yang bisa digunakan sesuai dengan tugas masing-masing.
Fitur yang Tersedia
- Login dan logout untuk pengguna.
- Dashboard yang menampilkan ringkasan inventori, seperti jumlah produk, supplier, nilai persediaan, stok rendah, dan transaksi stok.
- Manajemen produk: menambah, mengubah, menghapus, mencari, mengurutkan, dan memfilter data produk.
- Manajemen kategori untuk mengelompokkan produk.
- Manajemen supplier untuk menyimpan data pemasok barang.
- Stok masuk dan stok keluar, lengkap dengan jumlah, catatan, pengguna yang mencatat, dan histori transaksi.
- Peringatan stok rendah berdasarkan batas minimum stok produk.
- Laporan inventori dengan ringkasan transaksi dan filter tanggal.
- Manajemen pengguna untuk mengatur akun Admin dan Staff.
- Pembatasan hak akses sesuai role pengguna.
Teknologi yang Digunakan
- PHP 8.2 atau lebih baru
- Laravel 12
- Blade Template
- HTML dan CSS
- SQLite sebagai database default pada konfigurasi contoh
- Composer untuk mengelola dependency PHP
- Node.js dan npm untuk proses build aset frontend (jika diperlukan)
Cara Menjalankan Project
Langkah-langkah berikut ditulis untuk Windows menggunakan PowerShell. Pastikan PHP dan Composer sudah terpasang dan bisa dijalankan dari terminal.
1. Ekstrak dan buka folder project
Ekstrak ZIP project, lalu masuk ke folder project melalui terminal. Contoh:
cd path\ke\inventory_managament_W5_laravel-main
2. Install dependency Laravel
composer install
3. Buat file environment
Copy-Item .env.example .env
Kalau file .env sudah ada, tidak perlu menyalinnya lagi.
4. Siapkan database SQLite
Buat file database jika belum tersedia:
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File -Path database/database.sqlite }
Buka file .env, lalu pastikan pengaturan lokal berikut sesuai:
APP_NAME="Inventory Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
Jika masih ada baris konfigurasi DB_HOST, DB_PORT, DB_DATABASE, atau DB_USERNAME dari pengaturan database lain, konfigurasi tersebut bisa dikomentari saat menggunakan SQLite.
5. Generate application key
php artisan key:generate
6. Jalankan migration dan data contoh
php artisan migrate --seed
Perintah tersebut membuat tabel database dan mengisi data contoh untuk keperluan latihan. Jalankan pada database kosong saat pertama kali setup. Jangan menggunakan migrate:fresh pada database yang berisi data penting, karena perintah tersebut menghapus tabel beserta datanya.
7. Jalankan aplikasi
php artisan serve
Buka alamat berikut di browser:

http://127.0.0.1:8000

Untuk menghentikan server, tekan Ctrl + C pada terminal.
(Opsional) Build aset frontend
Jika ingin menjalankan proses build aset frontend, pastikan Node.js dan npm sudah terpasang, lalu jalankan:
npm install
npm run build
Akun Demo
Seeder menyediakan dua akun untuk mencoba aplikasi:
Role	Username	Password
Admin	admin	admin123
Staff	staff	staff123


Akun di atas hanya untuk pengembangan dan demo lokal. Jangan memakai password demo untuk aplikasi yang sudah dipublikasikan.
Hak Akses Pengguna
Fitur	Admin	Staff
Melihat dashboard	Ya	Ya
Melihat data produk	Ya	Ya
Menambah, mengubah, dan menghapus produk	Ya	Tidak
Mencatat stok masuk/keluar	Ya	Ya
Melihat laporan	Ya	Ya
Mengelola kategori	Ya	Tidak
Mengelola supplier	Ya	Tidak
Mengelola akun pengguna	Ya	Tidak


Struktur Folder Penting
app/
  Http/Controllers/   # Controller untuk fitur aplikasi
  Http/Middleware/    # Middleware pembatasan role
  Models/             # Model dan relasi database

database/
  migrations/         # Struktur tabel database
  seeders/            # Data contoh untuk pengujian lokal
  factories/          # Factory untuk membuat data contoh

resources/views/      # Halaman Blade
public/css/           # File CSS yang dipakai aplikasi
routes/web.php        # Daftar route aplikasi
Catatan
- Stok produk dikelola melalui transaksi barang masuk (IN) dan barang keluar (OUT). Produk baru dimulai dengan stok 0.
- Jumlah barang harus berupa bilangan bulat positif dan transaksi barang keluar tidak boleh melebihi stok yang tersedia.
- Data contoh yang dibuat oleh seeder bersifat acak, jadi jumlah atau isi data dapat berbeda setiap kali database diisi ulang.
- Untuk berpindah ke MySQL/XAMPP, ubah konfigurasi database di .env sesuai nama database, username, dan password MySQL, lalu jalankan migration pada database tersebut.
Penutup
Project ini dibuat sebagai latihan penerapan Laravel untuk pengelolaan data inventori, penggunaan database, CRUD, autentikasi, hak akses pengguna, dan pencatatan transaksi stok. README ini menjelaskan cara setup dasar agar project dapat dicoba secara lokal.
