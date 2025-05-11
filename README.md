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

## Demo
Kunjungi website DexTV: [https://dextv.wuaze.com/](https://dextv.wuaze.com/)

## Instalasi
1. Clone repository ke server lokal/web hosting.
2. Pastikan PHP, MySQL, dan web server (Apache/Nginx) sudah terpasang.
3. Import database jika ada.
4. Edit file `includes/config.php` untuk konfigurasi koneksi database dan path.
5. Jalankan di browser: `https://dextv.wuaze.com/`

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
