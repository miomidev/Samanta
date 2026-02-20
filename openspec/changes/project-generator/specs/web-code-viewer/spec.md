## ADDED Requirements

### Requirement: Pembaca Struktur Direktori Penuh
Sistem HARUS mengekspos API endpoint yang mengambil path direktori dari folder project yang telah di-generate, dan mengembalikan List API (JSON) berupa pohon direktori (Directory Tree).

#### Scenario: Mengembalikan List Direktori Dalam Folder `storage/app/generated_projects/<ID>`
- **WHEN** Akses Viewer meminta file tree untuk project A
- **THEN** API merespons dengan struktur parent-child folder dan file yang valid untuk project A.

### Requirement: Pembaca Konten File Proyek
Sistem HARUS memiliki API read file yang menerima path spesifik (relatif terhadap direktori project) untuk membaca PlainText atau Raw isi file seperti `.php`, `.js`, atau `.env`.

#### Scenario: Menampilkan Konten File Berhasil
- **WHEN** User mengklik file `.env` di UI Tree Panel
- **THEN** API /read-file dipanggil dan mengembalikan isian string teks kredensial atau config file tersebut.

### Requirement: Sandbox Path Directory
Sistem HARUS memvalidasi input user pada UI web-code-viewer sehingga request pembacaan direktori/file hanya diizinkan di dalam batas Path Project yang sedang diakses.

#### Scenario: Mencegah Path Traversal File Inclusion (LFI)
- **WHEN** Parameter request file dimanipulasi dengan `../../../path/to/server/.env`
- **THEN** Server menolak dengan respon 403 / 400 Bad Request, sehingga file utama dari aplikasi System Builder tidak bocor.
