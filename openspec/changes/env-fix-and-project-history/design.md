## Context

Setelah implementasi awal untuk `project-generator`, pengguna melaporkan isu duplikasi value kredensial database pada hasil copy file `.env`. Hal ini dikarenakan base Laravel >=11 menggunakan format konfigurasi `# DB_HOST=127.0.0.1` (commented) secara bawaan sehingga custom regex kita tidak replace baris komen tersebut melainkan append (menambah baru) sehingga menjadi terduplikasi. Selain itu, belum ada fitur "Riwayat Proyek" bagi user untuk menemukan kembali tautan ID `web-code-viewer` ke proyek yang mereka generate sebelumnya.

## Goals / Non-Goals

**Goals:**
- Merubah script `updateEnvironmentFile` agar menghiraukan baris komen dan menuliskan konfigurasi database dengan format bersih.
- Membuat tabel `projects` dan relasi ke `User` model.
- Membuat halaman UI untuk menampilkan List of Projects.

**Non-Goals:**
- Tidak memodifikasi logic tree explorer.
- Tidak menghandle backup online terhadap riwayat file. Hanya sekadar pencatatan Database.

## Decisions

- **Pembenahan Regex .env**: Alih-alih replace dengan regex baris per baris yang spesifik, kita bisa men-uncomment semua baris DB Configuration bawaan pada boilerplate, lalu me-replace valuenya secara akurat, ATAU memberikan baris `.env` baru tanpa mengubah konfigurasi original dengan mendelete keys yang memiliki relasi `# DB_...`. Solusi termudah yang kita angkat adalah **menghapus semua block DB yang lama**, lalu memastikan block kredensial DB yang baru ditulis secara clean dari input form.
- **Tabel `projects`**: Kolom yang dibutuhkan adalah `id` (UUID), `user_id`, `name`, `description`, `db_type`, `created_at`, dan `updated_at`. Relasi `BelongsTo` dari User -> Project.
- **Project History UI**: Tabel sederhana menggunakan TailwindCSS di dashboard.

## Risks / Trade-offs

- Penghapusan konfigurasi DB block dari `.env` bisa rentan missmatch jika format regex tidak kokoh. Mitigasinya adalah mereplace secara string function terhadap parameter-parameter standar Database tanpa menggunakan array looping yang kompleks.
