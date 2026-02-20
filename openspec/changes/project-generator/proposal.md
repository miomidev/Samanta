## Why

Sistem ini membutuhkan fitur untuk bertindak sebagai pembuat sistem (System Builder) yang memungkinkan pengguna untuk men-generate proyek Laravel baru secara dinamis. Hal ini akan menyederhanakan proses setup awal proyek dengan konfigurasi database kustom (seperti MySQL atau PostgreSQL), memberikan pengalaman "code builder" atau "AI builder" langsung dari antarmuka web.

## What Changes

- Penambahan antarmuka formulir (form) untuk membuat proyek baru (Nama Proyek, Deskripsi, Pilihan Tipe Database).
- Pembuatan form dinamis untuk pengisian kredensial database sesuai dengan tipe database yang dipilih (misal: MySQL, PostgreSQL, dll).
- Penambahan logika backend untuk men-generate struktur dasar proyek Laravel (boilerplate) dan meng-customize file `.env` berdasarkan input pengguna secara otomatis.
- Penambahan antarmuka Web File Viewer / Code Viewer di dalam dashboard yang bertindak seperti mini VSCode untuk menavigasi folder dan melihat isi kode file dari proyek yang berhasil di-generate.

## Capabilities

### New Capabilities
- `project-scaffolder`: Sistem inti untuk men-generate struktur base/boilerplate proyek Laravel baru ke dalam direktori tertentu.
- `env-generator`: Fitur spesifik untuk membuat dan menyesuaikan template kredensial `.env` sesuai dengan inputan user dan jenis database yang dipilih.
- `web-code-viewer`: Komponen UI (File Explorer & Code Editor readonly) dan API untuk membaca struktur direktori dan isi file dari proyek yang di-generate.

### Modified Capabilities
- 

## Impact

- Membutuhkan sistem penyimpanan / direktori khusus (Storage) untuk menyimpan folder instalasi proyek yang di-generator oleh pengguna.
- Eksekusi proses background pada server (misal, menyalin boilerplate atau menjalankan perintah composer) untuk membuat project.
- Membuka akses untuk pembacaan file lokal proyek yang di-generate agar bisa ditampilkan di `web-code-viewer`.
