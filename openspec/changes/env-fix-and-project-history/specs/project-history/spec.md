## ADDED Requirements

### Requirement: Tabel Riwayat Project (History)
Sistem HARUS menyimpan detail setiap project hasil generate pengguna ke dalam database melalui model Project. Tabel ini menyimpan nama, deskripsi, tipe koneksi db, dan menghubungkannya ke tabel users melalui kunci tamu (Foreign Key) `user_id`.

#### Scenario: Insert Data Project
- **WHEN** Pembuatan folder dan .env berhasil
- **THEN** Backend mencatat informasi project baru tersebut ke dalam database.

### Requirement: Tampilan Daftar Riwayat Proyek (UI)
Sistem HARUS menyediakan halaman bagi user yang masuk (Logged In) di mana mereka bisa melihat daftar semua proyek yang pernah mereka generate.

#### Scenario: Menampilkan List di UI
- **WHEN** Pengguna menavigasi ke halaman 'Project History'
- **THEN** Frontend menampilkan daftar proyek dalam format tabel / grid, dan menyertakan URL tautan untuk mengunjungi kembali 'Code Viewer' dari setiap proyek.
