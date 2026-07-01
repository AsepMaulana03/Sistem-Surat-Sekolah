# Rancangan Pengujian Blackbox

Pengujian *Blackbox* (Blackbox Testing) adalah metode pengujian perangkat lunak yang berfokus pada sisi fungsionalitas sistem tanpa perlu mengetahui atau melihat struktur kode internal program. Pengujian ini bertujuan untuk memastikan bahwa setiap fungsi dari sistem beroperasi sesuai dengan spesifikasi dan kebutuhan yang telah ditentukan. Penguji hanya memberikan input dan mengamati output yang dihasilkan, apakah sesuai dengan yang diharapkan atau tidak.

Berikut adalah deskripsi rancangan pengujian untuk fitur **Login** yang nantinya dapat Anda salin ke dalam format tabel di Microsoft Word:

## Skenario Pengujian: Form Login

1. **Pengujian Login Berhasil (Data Valid)**
   *   **Deskripsi:** Memastikan pengguna dapat masuk ke dalam sistem dengan menggunakan akun yang valid.
   *   **Data Uji:** Memasukkan *Username/Email* yang terdaftar secara valid dan *Password* yang benar.
   *   **Hasil yang Diharapkan:** Sistem menerima akses pengguna, mengarahkan pengguna ke halaman *Dashboard* (sesuai dengan hak akses/role-nya), dan menampilkan pesan sukses (misal: "Login berhasil").

2. **Pengujian Login Gagal (Username/Email Salah)**
   *   **Deskripsi:** Memastikan sistem menolak akses jika pengguna memasukkan username atau email yang tidak terdaftar.
   *   **Data Uji:** Memasukkan *Username/Email* yang **tidak** terdaftar dan *Password* yang benar.
   *   **Hasil yang Diharapkan:** Sistem menolak akses, halaman tetap berada di form login, dan menampilkan pesan peringatan (misal: "Email atau Password salah").

3. **Pengujian Login Gagal (Password Salah)**
   *   **Deskripsi:** Memastikan sistem menolak akses jika password yang dimasukkan tidak cocok dengan akun.
   *   **Data Uji:** Memasukkan *Username/Email* yang valid (terdaftar) namun *Password* **salah**.
   *   **Hasil yang Diharapkan:** Sistem menolak akses, halaman tetap berada di form login, dan menampilkan pesan peringatan (misal: "Email atau Password salah").

4. **Pengujian Login Gagal (Input Kosong)**
   *   **Deskripsi:** Memastikan sistem memiliki validasi pada form sehingga pengguna tidak bisa login dengan kolom yang kosong.
   *   **Data Uji:** Membiarkan kolom *Username/Email* dan *Password* dalam keadaan **kosong**, lalu menekan tombol "Login".
   *   **Hasil yang Diharapkan:** Sistem tidak memproses autentikasi, tidak melakukan *reload* halaman jika menggunakan validasi frontend, dan menampilkan pesan peringatan validasi (misal: "Email wajib diisi" dan "Password wajib diisi").

5. **Pengujian Login Keamanan Dasar (SQL Injection Check)**
   *   **Deskripsi:** Memastikan form login aman dari upaya manipulasi query database.
   *   **Data Uji:** Memasukkan karakter injeksi pada *Username* (contoh: `' OR '1'='1`) dan *Password* sembarang.
   *   **Hasil yang Diharapkan:** Sistem menolak akses dan menampilkan pesan error wajar tanpa membocorkan struktur database aplikasi.
