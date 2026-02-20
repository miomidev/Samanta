## Context

Aplikasi ini memerlukan fungsionalitas untuk men-generate sebuah project Laravel secara end-to-end lengkap dengan konfigurasi file environment (`.env`). Saat ini, jika seseorang ingin membuat dan mengatur proyek Laravel dengan database yang berbeda (MySQL, PostgreSQL, dll), mereka harus melakukannya secara manual lewat CLI. Fitur ini dirancang untuk memudahkan hal tersebut dengan menyediakan User Interface via web, sehingga pengguna dapat mengisi form kebutuhan, dan sistem akan mengonfigurasi `boilerplate` proyek secara dinamis, meng-update kredensial database, lalu memungkinkan pengguna untuk menjelajahi strukturnya melalui sebuah `Web Code Viewer` terintegrasi.

## Goals / Non-Goals

**Goals:**
- Sistem mampu menggandakan struktur awal proyek Laravel (boilerplate) ke direktori penyimpanan khusus (misalnya `storage/app/generated_projects`).
- Membuat sistem template `.env` yang fleksibel untuk berbagai format input database (seperti MySQL dan PostgreSQL).
- Menyediakan UI Form dinamis dari Frontend.
- Membangun API yang dapat membaca pohon direktori (directory tree) beserta isi konten file dari *generated project* secara real-time dan aman.
- Menampilkan UI File Explorer (Tree View) beserta Code Editor Viewer menggunakan komponen antarmuka yang modern layaknya VSCode.

**Non-Goals:**
- Pengguna **tidak** dapat mengedit konten file langsung dari web editor (bersifat ReadOnly).
- Sistem **tidak** akan menjalankan instalasi package otomatis (`composer install` atau `npm install`) pada project hasil generate untuk menghemat performa, storage, dan waktu eksekusi server (folder `vendor` dan `node_modules` diabaikan/tidak disertakan).
- Tidak mengurus proses deployment project.

## Decisions

1. **Scaffolding Mechanism:** 
   Alih-alih mengeksekusi perintah CLI `composer create-project` setiap kali ada request pembuatan proyek yang sangat bergantung pada koneksi internet dan memakan waktu lama, sistem akan menyimpan satu salinan **"Base Boilerplate Laravel"** clean yang belum memiliki folder `vendor` (untuk hemat storage). Request pembuatan project hanya menduplikasi *boilerplate* ini.
2. **Konfigurasi `.env`:** 
   Kita akan me-replace file `.env` yang ada di dalam boilerplate menggunakan fungsi manipulasi string/RegEx di PHP dengan membaca mapping input form dari pengguna.
3. **Penyimpanan (Storage):**
   Setiap project akan di-generate dengan Folder ID unik di dalam local disk Laravel (`storage/app/generated_projects/uniqid`).
4. **File Viewer:**
   Sistem membaca path dari direktori menggunakan fungsi rekursif standar PHP seperti `RecursiveDirectoryIterator`, mengubahnya ke dalam format JSON agar Vue/Alpine/Blade (Frontend) bisa mengubahnya menjadi komponen tree structure.

## Risks / Trade-offs

- **[Risk] Keamanan: Path Traversal Attack pada API Reader** ➔ Seseorang mencoba memanipulasi parameter API `path` (misal dengan `../../.env`) untuk membaca sistem utama web kita.
  - **Mitigation:** Semua Path API harus disaring (sanitized) dengan ketat, divalidasi dengan `realpath()`, dan memastikan jalurnya tetap terkunci (sandboxed) di dalam base direktori project masing-masing.
- **[Risk] Beban Server dan Penyimpanan Penuh** ➔ Terlalu banyak project yang dibuat membebani ruang `storage/app/`.
  - **Mitigation:** Implementasi auto-delete untuk folder dari project lama yang sudah melebihi 24 jam atau memberikan tombol manual 'Delete Project' secara eksplisit bagi pengguna.
