## MODIFIED Requirements

### Requirement: Duplikasi Boilerplate
**Reason**: Kebutuhan menginstal versi Laravel terbaru dan merasakan experience layaknya developer sungguhan.
**Migration**: Ganti logic controller yang semula copying `.app/laravel-boilerplate` menjadi sinkronisasi Shell command `composer create-project laravel/laravel`.

Sistem HARUS mengeksekusi perintah CLI `composer create-project` dan menangkap output buffer log-nya ke sebuah file temporer sehingga frontend UI dapat melakukan polling API pembacaan progress pembuatan project.

#### Scenario: Menjalankan Composer dan Mem-polling Status
- **WHEN** user men-submit form database (dengan ajax POST `/project/generate-async`),
- **THEN** backend merespon dengan `job_id` unik dan segera menjalankan `composer` dalam background process. UI beralih ke layar ala terminal dan mem-polling `/project/progress/{job_id}` untuk melihat baris-baris instalasi yang ditambahkan secara *real-time*. Setelah komposer usai, .env ditulis, dan database di-*update*.
