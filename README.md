# Sacket - Aplikasi Web Ticketing Event

### [**🚀 Lihat Demo Langsung**](https://sacket-x0j8k.sevalla.app/)

Sacket adalah aplikasi web *full-stack* yang dibangun menggunakan Laravel 11 untuk penjualan tiket event. Aplikasi ini menyediakan platform yang lengkap bagi pengguna untuk menemukan dan membeli tiket, serta panel admin yang canggih bagi pengelola untuk mengatur event, penjualan, dan operasional.

## Fitur Utama ✨

### Untuk Pengguna (Pembeli Tiket)
* **Dashboard Event Modern:** Halaman utama dinamis dengan *carousel* event unggulan dan galeri event.
* **Pencarian & Filter:** Kemampuan untuk mencari event berdasarkan nama atau lokasi.
* **Alur Pemesanan Lengkap:** Pengguna dapat memilih kategori tiket, jumlah, dan menerapkan kode promo.
* **Sistem Kode Promo:** Fungsionalitas diskon (potongan harga tetap atau persentase) yang divalidasi secara *real-time*.
* **Integrasi Pembayaran:** Terhubung dengan Midtrans untuk berbagai metode pembayaran.
* **Area "My Tickets":** Halaman khusus bagi pengguna untuk melihat riwayat pesanan dan status pembayaran.
* **Download E-Ticket:** Pengguna dapat mengunduh tiket PDF yang berisi QR Code unik.
* **Sistem Autentikasi Modern:** Halaman *login* dan *register* yang bersih, serta *navbar* dengan menu avatar pengguna.

### Untuk Admin & Panitia
* **Panel Admin Canggih (Filament):** *Dashboard* terpusat yang aman untuk mengelola seluruh aplikasi.
* **Dashboard Statistik:** Menampilkan ringkasan pendapatan, jumlah tiket terjual, dan event aktif.
* **Manajemen Event (CRUD):** Kemampuan penuh untuk membuat, mengedit, dan menghapus data event, termasuk *upload* gambar.
* **Manajemen Kategori Tiket (CRUD):** Mengelola jenis tiket (VIP, Presale, dll.) untuk setiap event.
* **Manajemen Kode Promo (CRUD):** Mengelola sistem diskon.
* **Melihat Daftar Pesanan:** Memantau semua transaksi yang masuk.
* **Sistem Check-in Tiket:** Halaman `/scanner` khusus untuk memvalidasi tiket di hari-H menggunakan kamera.

### Keamanan & Role
* **Hak Akses Berbasis Peran:** Sistem dibagi menjadi tiga peran: `admin`, `scanner`, dan `user`.
* **Middleware Fleksibel:** Rute dilindungi berdasarkan peran pengguna. Admin memiliki akses penuh, sementara *scanner* hanya bisa mengakses halaman *scanner* dan profil.
* **Keamanan Terpusat:** Menggunakan *package* `spatie/laravel-permission` untuk manajemen *role* yang profesional.

---
## Teknologi yang Digunakan 💻

* **Backend:** Laravel 11
* **Frontend:** Blade, Tailwind CSS, Alpine.js
* **Panel Admin:** Filament 3
* **Database:** MySQL / PostgreSQL
* **Pembayaran:** Midtrans
* **Lainnya:** `spatie/laravel-permission`, `barryvdh/laravel-dompdf`, `simplesoftwareio/simple-qrcode`

---
## Panduan Instalasi Lokal

1.  **Clone repositori:**
    ```bash
    git clone [https://github.com/NAMA_ANDA/ticketing-project.git](https://github.com/NAMA_ANDA/ticketing-project.git)
    cd ticketing-project
    ```
2.  **Install dependensi:**
    ```bash
    composer install
    npm install
    ```
3.  **Buat file `.env`:**
    ```bash
    cp .env.example .env
    ```
4.  **Generate `APP_KEY`:**
    ```bash
    php artisan key:generate
    ```
5.  **Konfigurasi `.env`:** Atur koneksi database (`DB_*`), kredensial Midtrans, dan Mailtrap.
6.  **Jalankan Migrasi & Seeder:**
    ```bash
    php artisan migrate:fresh --seed
    ```
7.  **Buat *Symbolic Link* untuk Storage:**
    ```bash
    php artisan storage:link
    ```
8.  **Jalankan Aplikasi:**
    ```bash
    # Jalankan server development
    php artisan serve
    # Jalankan server Vite untuk aset frontend
    npm run dev
    # (Opsional) Jalankan queue worker jika email diaktifkan
    # php artisan queue:work
    ```

---
## Kredensial Default

Setelah menjalankan *seeder*, Anda bisa login menggunakan akun berikut:

* **Admin:**
    * **Email:** `admin@example.com`
    * **Password:** `password`
* **Scanner:**
    * **Email:** `scanner@example.com`
    * **Password:** `password`
* **User Biasa:**
    * **Email:** `user@example.com`
    * **Password:** `password`
