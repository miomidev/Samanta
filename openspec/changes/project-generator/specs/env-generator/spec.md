## ADDED Requirements

### Requirement: Deteksi Input Kredensial Database
Sistem HARUS membaca data array/string kredensial database (host, user, pass, db name, type) dari inputan form user.

#### Scenario: User Menginputkan Konfigurasi PostgreSQL Berhasil
- **WHEN** user memilih tipe PostgreSQL dan mengisi parameter kredensial
- **THEN** Object Mapping di backend mengubah string `.env` target (dari boilerplate asli) menjadi DB_CONNECTION=pgsql, DB_USERNAME=user, dll

### Requirement: Penulisan File .env
Sistem HARUS men-overwrite atau meng-update isi file `.env` yang berada di dalam folder project target (hasil `project-scaffolder`).

#### Scenario: File `.env` Target Berubah
- **WHEN** Copy boilerplate selesai, modul env-generator di triger
- **THEN** Modul mencari baris-baris kredensial `DB_` pada `.env` di direktori baru dan mereplace isinya dengan regex/string matcher.
