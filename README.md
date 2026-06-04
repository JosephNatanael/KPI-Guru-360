# 🏆 KPI-360-Guru

<p align="center">
  <strong>Sistem Penilaian Kinerja Guru (KPI) 360 Derajat Berbasis Web</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5.3">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📌 Tentang & Tujuan Project

**KPI-360-Guru** adalah aplikasi web yang dirancang khusus untuk melakukan penilaian kinerja guru secara objektif, komprehensif, dan transparan menggunakan metode **Penilaian 360 Derajat**. 

Berbeda dengan penilaian tradisional yang hanya dilakukan secara searah oleh Kepala Sekolah, sistem ini mengumpulkan umpan balik dari berbagai sudut pandang (multi-rater) untuk menghasilkan evaluasi yang adil dan membangun.

### 🎯 Tujuan Utama:
1. **Objektivitas Tinggi**: Meminimalisir subjektivitas penilaian dengan mengintegrasikan perspektif dari Kepala Sekolah, rekan sejawat (sesama guru), wali murid (orang tua), serta evaluasi diri.
2. **Standardisasi Kompetensi**: Mengukur kinerja guru berdasarkan 4 Kompetensi Guru Nasional: **Pedagogik**, **Kepribadian**, **Sosial**, dan **Profesional**.
3. **Pengambilan Keputusan Berbasis Data**: Membantu manajemen sekolah memberikan rekomendasi pembinaan yang tepat (seperti promosi, pelatihan, atau evaluasi) berdasarkan hasil penilaian akhir yang terukur.
4. **Efisiensi Administrasi**: Mengotomatisasi rekapitulasi nilai, perhitungan bobot penilai, dan pencetakan laporan kinerja individual (PDF).

---

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan kombinasi teknologi modern untuk menjamin performa, keamanan, dan keindahan tampilan:

| Komponen | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Framework Utama** | [Laravel 12.x](https://laravel.com/) | Framework PHP modern dengan arsitektur MVC yang aman dan efisien. |
| **Bahasa Pemrograman** | PHP 8.2+ | Menggunakan fitur PHP terbaru untuk optimasi kode. |
| **Database** | MySQL | Penyimpanan data relasional untuk transaksi penilaian, user, dan master data. |
| **Desain & UI** | Tailwind CSS v4 & Bootstrap 5.3 | Kombinasi keindahan dan fleksibilitas Tailwind CSS dengan komponen responsif dari Bootstrap. |
| **Asset Bundler** | [Vite](https://vitejs.dev/) | Compiler frontend super cepat untuk mengelola CSS/JS. |
| **PDF Generator** | [Dompdf](https://github.com/barryvdh/laravel-dompdf) | Memproses template Blade menjadi file PDF siap cetak. |
| **Interaksi UI** | SweetAlert2 | Pop-up alert interaktif dan modern untuk konfirmasi tindakan. |

---

## 🌟 Fitur Utama

Sistem ini memiliki fitur lengkap yang mencakup pengelolaan master data hingga pelaporan akhir:

### 👤 1. Manajemen Multi-Role (RBAC)
Mendukung 4 level pengguna dengan hak akses yang terproteksi:
*   **Admin**: Mengelola master data guru, user login, wali murid, periode akademik, bobot penilaian, dan rekomendasi.
*   **Kepala Sekolah**: Menilai guru, mengelola indikator KPI, memantau dashboard statistik, melihat riwayat, serta mencetak laporan PDF.
*   **Guru**: Melakukan evaluasi mandiri (self-assessment), melakukan penilaian sejawat terhadap rekan guru lain, melihat dashboard performa pribadi.
*   **Wali Murid**: Mengisi penilaian khusus untuk wali kelas dari anaknya secara terproteksi.

### 📊 2. Dashboard Analitik & Statistik
*   Visualisasi rata-rata nilai kompetensi guru menggunakan grafik.
*   Statistik penyelesaian penilaian (guru mana yang sudah dinilai/belum dinilai oleh setiap role).
*   Summary performa untuk membantu Kepala Sekolah mengambil keputusan cepat.

### 📐 3. Fleksibilitas Indikator KPI & Soal
*   Pengelompokan indikator berdasarkan 4 kompetensi utama.
*   Manajemen bank soal/pertanyaan per indikator.
*   Fitur **Copy Questions** dari periode sebelumnya untuk menghemat waktu pengaturan di periode baru.

### ⚖️ 4. Pengaturan Bobot Penilai Dinamis
Mendukung skenario pembobotan yang berbeda berdasarkan peran guru:
*   **Wali Kelas**: Kepala Sekolah (50%), Rekan Guru (30%), Wali Murid (20%).
*   **Non-Wali Kelas**: Kepala Sekolah (70%), Rekan Guru (30%).
*   Bobot ini dapat disesuaikan kapan saja oleh Administrator melalui menu pengaturan bobot.

### 🧮 5. Penghitungan Nilai Akhir Otomatis (Final Score)
*   Sistem secara otomatis mendeteksi status kelengkapan penilai sebelum melakukan kalkulasi.
*   Menghitung nilai akhir 360 derajat berdasarkan bobot masing-masing penilai yang aktif.

### 💡 6. Rekomendasi Kinerja Otomatis
*   Menghubungkan nilai akhir dengan rekomendasi tindakan nyata secara otomatis, seperti:
    *   **Promosi** (Nilai $\ge$ 4.5)
    *   **Pelatihan** (Nilai 4.0 - 4.49)
    *   **Pembinaan** (Nilai 3.5 - 3.99)
    *   **Evaluasi Khusus** (Nilai $<$ 3.5)

### 🖨️ 7. Laporan & Cetak PDF (Dompdf)
*   Cetak rapor kinerja guru secara individual dengan detail per indikator.
*   Fitur **Cetak Semua** bagi Kepala Sekolah untuk mengunduh seluruh laporan guru sekaligus dalam format PDF.

---

## 🚀 Panduan Instalasi & Pengembangan

Aplikasi ini dilengkapi dengan script otomatis untuk mempermudah setup awal.

### 📋 Prasyarat
*   PHP >= 8.2 (dengan ekstensi PDO, OpenSSL, Mbstring, XML)
*   Composer
*   Node.js & NPM
*   MySQL / MariaDB

### ⚙️ Cara Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://github.com/JosephNatanael/KPI-Guru-360.git
    cd KPI-Guru-360
    ```

2.  **Jalankan Auto-Setup Script**
    Aplikasi ini menyediakan shortcut setup yang secara otomatis menginstal dependensi PHP/NPM, menyalin `.env`, membuat app key, menjalankan migrasi database, dan mem-build asset:
    ```bash
    composer run setup
    ```

3.  **Import Database / Seeders**
    Jika ingin menggunakan data dummy pengujian yang sudah lengkap dengan skenario penilaian:
    ```bash
    php artisan db:seed --class=DatabaseSeeder
    # Atau jalankan khusus Dummy Data Seeder
    php artisan db:seed --class=DummyDataSeeder
    ```

4.  **Menjalankan Server Lokal**
    Untuk menjalankan server lokal beserta Vite compiler secara bersamaan dalam satu command:
    ```bash
    composer run dev
    ```
    Sistem akan otomatis berjalan di `http://localhost:8000`.

---

## 🔒 Informasi Akun Demo (Default Seed)

Gunakan akun berikut setelah menjalankan database seeder untuk pengujian:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Kepala Sekolah** | `kepsek@sekolah.sch.id` | `kepsek123` |
| **Guru (Wali Kelas 7A)** | `budisantoso@guru.sch.id` | `guru123` |
| **Wali Murid (Kelas 7A)** | `ahmad.hidayat@email.com` | `walimurid123` |

---

## 📄 Lisensi
Project ini menggunakan lisensi **MIT License** - lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.
