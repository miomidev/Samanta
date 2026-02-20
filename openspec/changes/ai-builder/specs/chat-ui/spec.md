## MODIFIED Requirements

### Requirement: Layout UI Chat Pada Component Viewer
Sistem HARUS memodifikasi area editor di `resources/views/page/project/viewer.blade.php`. Jika sebelumnya hanya ada Sidebar Kiri (Explorer) dan Editor Tengah, sekarang panel Editor harus dibagi dua secara horizontal atau vertikal: sebagian untuk teks file code (`<textarea>` / component), dan sebagian (kolom kanan) sebagai UI Panel Chat layaknya *Cursor* atau *GitHub Copilot Chat*.

#### Scenario: Interaksi UI Chat AI
- **WHEN** user membuka halaman viewer project ter-generate,
- **THEN** Mereka akan melihat sebuah panel obrolan AI yang memiliki text input (prompt), indikator "Gemini Thinking...", serta tombol kirim.

#### Scenario: Animasi Loading Generate AI
- **WHEN** user men-submit form pendaftaran Project Baru atau instruksi prompt Chat,
- **THEN** Pada Generate System UI, tampilkan animasi CSS Terminal (meng-scroll baris console otomatis), serta di dalam UI obrolan, perlihatkan label / animasi *skeleton* atau sejenisnya selagi API gemini diproses backend hingga file tersebut otomatis termodify.
