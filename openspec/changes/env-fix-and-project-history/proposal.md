## Why

Pada implementasi Project Generator sebelumnya, ditemukan isu di mana konfigurasi `.env` hasil generate mengalami duplikasi pada blok kredensial database. Selain itu, pengguna saat ini tidak dapat melihat riwayat project apa saja yang sudah berhasil mereka buat. Oleh karena itu, kita perlu memperbaiki fungsi regex di `env-generator` dan menambahkan sistem penyimpanan riwayat project ke dalam database agar pengguna dapat melihat list project mereka. 

## What Changes

- Modifikasi fungsi `updateEnvironmentFile` pada `ProjectGeneratorController` agar bisa mendeteksi dan meng-uncomment baris konfigurasi database (`env`) dengan benar atau menghapus konfigurasi lama dan me-replace dengan yang baru tanpa duplikasi.
- Menambahkan tabel `projects` ke dalam database untuk menyimpan riwayat pembuatan project (Nama, path, ID, User ID, Date).
- Menambahkan halaman UI "Project History" yang merender daftar proyek dari database untuk melihat riwayat atau membuka kembali file explorer project tersebut.

## Capabilities

### New Capabilities
- `project-history`: Kemampuan sistem untuk mencatat setiap project yang dibuat oleh user ke dalam database dan menampilkannya di halaman web (Riwayat Project).

### Modified Capabilities
- `env-generator`: Modifikasi regex dan logic penggantian kredensial `.env` untuk menghindari duplikasi key konfigurasi database.

## Impact

- Membutuhkan pembuatan Migration model `Project` di database.
- Terdapat penyesuaian pada Controller saat men-generate project agar data disubmit langsung ke database.
