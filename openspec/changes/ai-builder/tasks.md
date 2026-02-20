## 1. Environment & Packages Setup
- [x] 1.1 Tambahkan variable lingkungan `GEMINI_API_KEY` di file `.env` sistem menggunakan API key yang diberikan oleh User dari prompt awal.

## 2. Terminal UI (Composer Scaffolding Backend)
- [x] 2.1 Update `ProjectGeneratorController@generate` untuk mengeksekusi `Symfony\Component\Process\Process::fromShellCommandline('composer create-project laravel/laravel "' . $targetPath . '"')`. 
- [x] 2.2 Tangkap proses output Command-Line dan alirkan teksnya ke file log yang unik `storage/logs/project-build-{id}.log` saat instalasi berjalan dalam antrean background.
- [x] 2.3 Buat endpoint polling `GET /project/{id}/stream-log` untuk membaca dan mengembalikan seluruh string baris log yang tercatat untuk ID project tersebut.

## 3. Terminal UI (Frontend)
- [x] 3.1 Ubah tampilan pengajuan di `project.create.blade.php`: Saat tombol generate di-submit, ganti view menjadi layar Animasi Terminal CSS/div berlatar hitam.
- [x] 3.2 Implementasikan AlpineJS AJAX Polling ke endpoint `/stream-log` setiap 2 detik untuk menambahkan teks ke textarea terminal CLI. Scroll ke bawah otomatis.
- [x] 3.3 Lakukan pengalihan otomatis ke halaman Viewer jika poling mendeteksi kata "Database Configurations" telah tertulis atau instalasi selesai di endpoint log.

## 4. Chat UI Integration (Viewer Panel)
- [x] 4.1 Pisahkan area antarmuka `project.viewer.blade.php` menjadi Editor Tengah (TextArea) dan Chat Panel Kanan (Balon Obrolan, Input Text, Tombol Send).
- [x] 4.2 Konfigurasikan Model data interaksi percakapan di `Alpine` (`messages: []`).

## 5. Gemini API Code Re-writing Endpoint
- [x] 5.1 Buat `POST /project/{id}/ai-chat` di router web lengkap dengan param JSON input Prompt pengguna.
- [x] 5.2 Di file Controller `ProjectGeneratorWebController` atau spesifik AI, ambil *isi file* dari `active_file_path` yang dibuka user, lalu kirim ke `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={API_KEY}` via cURL Laravel HTTP Client.
- [x] 5.3 Konstruksi *System Instructions Prompt* strict ke Gemini agar hanya membalas format JSON `{"edited_content": "..."}` atau pesan informatif jika error.
- [x] 5.4 Backend akan save `File::put()` konten baru Gemini ke path project target. Response sukses dikirim.

## 6. Interaksi Feedback 
- [x] 6.1 Setelah submit Chat sukses, View me-refresh *Code Textarea* dengan kode baru dari backend. Berikan transisi Blink Highlight sederhana pada UI elemen via JS Class.
