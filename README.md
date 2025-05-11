# DexTV

DexTV adalah platform streaming TV online yang menyediakan berbagai channel lokal dan internasional dengan kualitas HD, akses global, dan streaming 24/7.

## Fitur
- Streaming TV online berbagai kategori (news, sport, cartoon, lokal, event)
- Dashboard admin untuk kelola channel (add, edit, delete, aktif/nonaktif)
- Cek status URL channel (online/offline) secara otomatis
- Sistem login/register user
- UI modern dan responsif

## Developer
**Sayyid Abdullah Azzam**

## Instalasi
1. Clone repository ke server lokal atau web hosting.
2. Pastikan PHP dan web server (Apache/Nginx) sudah terpasang.
3. Data channel disimpan di file JSON, tidak perlu setup database MySQL.
4. Edit file `includes/config.php` untuk konfigurasi path jika diperlukan.
5. Jalankan di browser: `http://localhost/DexTV/` atau akses DexTv

## Keamanan
- Semua input divalidasi di sisi server dan client.
- Session management untuk autentikasi user dan admin.
- Data channel disimpan di file JSON dengan pengecekan akses.
- Admin panel hanya bisa diakses oleh user dengan role admin.
- Password user di-hash (gunakan bcrypt/argon2).
- Proteksi CSRF pada form penting.
- Escape output untuk mencegah XSS.
- Tidak menampilkan error detail ke user (hanya log ke file/error_log).
- File upload (jika ada) dibatasi tipe dan ukuran.

## Lisensi
Proyek ini hanya untuk pembelajaran dan pengembangan internal.
