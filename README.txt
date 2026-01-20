Project Pemilihan Online

Sistem Project Pemilihan Online adalah aplikasi berbasis web yang digunakan untuk menyelenggarakan proses pemilihan secara digital, mulai dari pendaftaran pengguna, pengelolaan kandidat, proses voting, hingga penampilan hasil pemilihan secara real-time. Sistem ini dirancang untuk mempermudah pelaksanaan pemilihan yang aman, cepat, dan transparan.

Aplikasi ini mendukung dua jenis pengguna utama, yaitu Panitia dan Pemilih, dengan hak akses dan fitur yang berbeda sesuai peran masing-masing. Sistem dibangun menggunakan PHP Native, terhubung dengan database MySQL, serta dilengkapi mekanisme session management dan kode unik untuk menjamin keabsahan suara.

Fitur Utama
1. Hak Akses Multi-Role

Admin / Panitia

Mengelola data kandidat.

Mengelola data pemilih.

Melihat dan memantau hasil voting.

Mengatur jalannya proses pemilihan.

Pemilih

Melakukan registrasi akun.

Login ke sistem menggunakan akun terdaftar.

Melakukan voting satu kali sesuai hak pilih.

Melihat hasil pemilihan setelah voting selesai.

2. Sistem Voting Online

Halaman voting dengan daftar kandidat.

Setiap pemilih hanya dapat memberikan satu suara.

Validasi status pemilih sebelum melakukan voting.

Penggunaan kode unik untuk mencegah voting ganda.

3. Manajemen Kandidat

Upload dan pengelolaan foto kandidat.

Penampilan profil kandidat pada halaman voting.

Penyimpanan data kandidat di database.

4. Hasil Pemilihan (Result)

Perhitungan suara otomatis.

Penampilan hasil voting secara real-time.

Tampilan grafik dan data hasil pemilihan.

Transparansi jumlah suara setiap kandidat.

5. Dashboard Per Role

Dashboard Panitia

Informasi jumlah pemilih.

Informasi jumlah kandidat.

Monitoring hasil pemilihan.

Dashboard Pemilih

Akses langsung ke halaman voting.

Teknologi yang Digunakan
Komponen	        Teknologi
Bahasa Pemrograman	PHP 8+
Web Server     	Apache (XAMPP)
Database	          MySQL
PDF Generator     	TCPDF
Frontend	      HTML dan CSS
Session & Auth	  PHP Session
Keamanan      Validasi Session & Kode Unik

Struktur Folder (Ringkasan)
├── Web_Pemilihan_Online/
├── HTML/
│   ├── CSS/               # File CSS
│   ├── images/            # Gambar website
│   ├── foto_kandidat/     # Foto kandidat
│   ├── navbar/            # seluruh menu navbar
│   ├── Landing.php        # Halaman utama
│   ├── Login.php          # Halaman login
│   ├── Register.php       # Halaman registrasi
│   ├── Voting.php         # Halaman voting
│   ├── Result.php         # Halaman hasil voting
│   ├── Panitia.php        # Dashboard panitia
│   ├── koneksi.php        # Koneksi database
│   └── logout.php         # Logout session
│
└── ...

Instalasi
1. Siapkan Lingkungan

Install XAMPP

Pastikan Apache & MySQL berjalan

2. Extract Project

Tempatkan folder ini ke:

C:\xampp\htdocs\project_pemilihan_online

3. Import Database

Buka phpMyAdmin

Buat database baru, misalnya:

project_dokumen


Import file .sql yang tersedia (jika ada)

4. Konfigurasi Koneksi Database

Edit file:

includes/config.php


Isi dengan parameter server lokal Anda:

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'poject_pemilihan_online';

Menjalankan Aplikasi

Buka browser dan akses:

http://localhost/project_pemilihan_online/


Login sesuai role.
Jika pertama kali, buat akun atau gunakan data default (jika tersedia).

Keamanan Sistem

Session Management

Session digunakan untuk mengontrol akses halaman.

Pemilih tidak dapat voting lebih dari satu kali.

Kode Unik Voting

Digunakan untuk mencegah manipulasi dan voting ganda.

Validasi Akses

Halaman panitia tidak dapat diakses oleh pemilih.

Lisensi

Project ini bersifat privat dan digunakan untuk keperluan akademik dan internal, serta tidak diperbolehkan untuk diperjualbelikan tanpa izin pengembang.

Kontak

Jika membutuhkan bantuan atau pengembangan lanjutan, silakan hubungi Developer.
