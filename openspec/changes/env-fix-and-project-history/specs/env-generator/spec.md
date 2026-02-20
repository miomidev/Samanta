## MODIFIED Requirements

### Requirement: Penulisan File .env
Sistem HARUS men-overwrite atau meng-update isi file `.env` yang berada di dalam folder project target (hasil `project-scaffolder`) tanpa menyebabkan duplikasi parameter konfigurasi database dan menghapus konfigurasi default/bawaan yang menggunakan gaya tanda komentar (`# DB_HOST=...`).

#### Scenario: File `.env` Target Berubah Tanpa Duplikasi
- **WHEN** Copy boilerplate selesai, modul env-generator di-trigger
- **THEN** Modul memastikan seluruh tag kredensial asli database dihapus/dibersihkan dari file `.env`, lalu menuliskan tag DB baru secara berurut dengan kredensial dari input user. Parameter opsional seperti password dapat dibiarkan kosong, dan format value ditulis dengan bersih.
