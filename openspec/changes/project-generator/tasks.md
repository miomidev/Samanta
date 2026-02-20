## 1. Setup Boilerplate
- [x] 1.1 Siapkan folder `storage/app/laravel-boilerplate` yang berisi clean instalasi Laravel (tanpa vendor/node_modules).

## 2. Server-side API & Controller
- [x] 2.1 Buat rute web / api untuk project generation: `POST /api/project/generate`.
- [x] 2.2 Buat `ProjectGeneratorController` dengan method utama untuk menangani request pembuatan project.
- [x] 2.3 Buat sistem ID Unik untuk penamaan folder (e.g. `Str::uuid()` atau string random).
- [x] 2.4 Implementasikan fungsi `copyDirectory` dari boilerplate ke folder target (`storage/app/generated_projects/{ID}`).

## 3. Environment Configurator (.env Generator)
- [x] 3.1 Buat service class / fungsi helper untuk membaca file `.env` di path project yang baru digenerate.
- [x] 3.2 Terapkan fungsi Regex (atau manipulasi string `DB_*`) untuk me-replace value kredensial berdarasarkan input form user (MySQL, PostgreSQL, dll).
- [x] 3.3 Simpan (overwrite) ulang file `.env` dengan data yang sudah terupdate.

## 4. Web Code Viewer API
- [x] 4.1 Buat endpoint `GET /api/project/{id}/tree` untuk mengembalikan list directory tree (Folder dan File) dalam format JSON dari project target. (Gunakan fungsi rekursif PHP atau `Symfony\Component\Finder\Finder`).
- [x] 4.2 Terapkan filter untuk menyembunyikan/menolak file berbahaya dan mencegah directory traversal pada `/{id}/tree`.
- [x] 4.3 Buat endpoint `GET /api/project/{id}/file` yang menerima URL parameter `?path=...` untuk membaca isi text file (readonly). Validasi path agar hanya terbatas di root folder project tersebut (Sandbox).

## 5. UI Frontend (Blade / Vue / Alpine)
- [x] 5.1 Buat halaman View `project.create` berisi form Input: Nama Project, Deskripsi, Tipe Database (Dropdown), Host, User, Pass.
- [x] 5.2 Implementasikan Fetch/Axios request pada form submit ke `POST /api/project/generate` lengkap dengan indikator loading.
- [x] 5.3 Buat halaman View `project.viewer` (Code/File Viewer UI).
- [x] 5.4 Integrasikan UI dengan sidebar `Tree View` kiri (memanggil endpoint `/tree`) yang bisa di-click / expand.
- [x] 5.5 Integrasikan area editor kanan untuk menampilkan kode file (menggunakan package frontend editor viewer jika ada, atau sekadar blok `<pre><code>` dasar) yang terpilih dari tree sidebar.

## 6. Testing & Finalisasi
- [x] 6.1 Lakukan end-to-end testing dari form -> generate project -> cek .env -> viewer UI.
- [x] 6.2 Perbaiki bug yang mungkin muncul pada folder permission atau UI rendering.
