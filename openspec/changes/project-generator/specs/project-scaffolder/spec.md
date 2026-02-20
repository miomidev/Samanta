## ADDED Requirements

### Requirement: Duplikasi Boilerplate
Sistem HARUS mampu menduplikasi folder instalasi *clean* Laravel (boilerplate) ke direktori tujuan (storage) yang unik untuk setiap project baru.

#### Scenario: Pembuatan Project Baru Berhasil
- **WHEN** Pengguna mengisi form pembuatan project dan melakukan submit
- **THEN** Sistem menyalin seluruh isi folder boilerplate ke folder `storage/app/generated_projects/<ID_UNIK>` tanpa folder vendor/node_modules

### Requirement: Penamaan Direktori Project
Sistem HARUS menyimpan setiap project di dalam folder terpisah dengan ID yang unik agar tidak terjadi file collision (bentrok file proyek) di dalam storage.

#### Scenario: Identifier Unik per Proyek
- **WHEN** Ada request project generation yang masuk secara bersamaan
- **THEN** Setiap project disimpan dengan Folder ID UUID atau format Acak yang terisolasi
