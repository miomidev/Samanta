## ADDED Requirements

### Requirement: Editor Kode Cerdas (Gemini AI API)
Sistem HARUS menyediakan API Endpoint `/project/{id}/ai-chat` di mana user bisa mengirim instruksi pesan natural language untuk memodifikasi file project saat ini yang sudah terbuka, atau membuat file baru di web viewer. 

#### Scenario: Code Editor Mengubah Teks
- **WHEN** user menekan "Send" di jendela Chat.
- **THEN** Model Backend akan mengirim payload konteks (Kode sumber file saat ini + file path-nya) beserta pesan Prompt user ke Endpoint API `generativelanguage.googleapis.com` dengan metode JSON terstruktur. 

#### Scenario: Menulis File Hasil Editor dari AI
- **WHEN** Jawaban format JSON diterima dari Gemini.
- **THEN** Backend secara otomatis membuka dan menulis-tindih (overwrite) ke file path spesifik yang ditentukan AI pada server storage `storage/app/generated_project/{id}`, lalu mengembalikan update ke View untuk men-sync kode editor secara *live* disertai animasi highlight/sukses.
