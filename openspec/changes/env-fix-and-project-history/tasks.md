## 1. Database Schema (Project History)
- [x] 1.1 Buat model Migration `add_projects_table` (atau `php artisan make:model Project -m`). Isinya: nama, id unik string, deskripsi, db jenis, user_id, timestamps.
- [x] 1.2 Edit Relasi Model: Tambahkan `hasMany(Project::class)` di Model User, dan `belongsTo(User::class)` di Model Project.
- [x] 1.3 Jalankan migrate.

## 2. Server-side Fix (`env-generator` & API History)
- [x] 2.1 Perbaiki `updateEnvironmentFile` di `ProjectGeneratorController`. Logikanya: gunakan regex untuk mereplace/menghapus semua line yang diawali `DB_*` (termasuk comment `# DB_*`), kemudian di akhir block sisipkan baris konfigurasi file `.env` yang baru (Host, Port, DB Name, User, dsb).
- [x] 2.2 Pada proses Generate berhasil, insert data project ke database: `$request->user()->projects()->create([...])`
- [x] 2.3 Buat API Endpoint atau rute Web yang menampilkan View untuk `Project History` milik user yang sedang aktif.

## 3. UI Project History
- [x] 3.1 Buat template View `project.history` (e.g. `resources/views/page/project/history.blade.php`).
- [x] 3.2 Tampilkan list project sebagai iterasi `@foreach ($projects as $project)` dalam grid atau table styling ringan. Harus ada tombol link "Buka View Project" ke URI `{id}/viewer`.
- [x] 3.3 Tambahkan Web Menu Link (navigation/sidebar) yang mengarahkan ke "/projects" History Page supaya user mudah menavigasinya dari Dashboard utama.
