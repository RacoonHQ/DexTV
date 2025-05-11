<?php
require_once '../includes/functions.php';

// Endpoint ini digunakan ketika:
// 1. Mengambil daftar channel untuk kategori tertentu
// 2. Diakses melalui AJAX dari javascript
// Contoh URL: /DexTV/api/channels.php?category=news

if (isset($_GET['category'])) {
    // Menggunakan fungsi getChannelsByCategory dari functions.php
    // yang akan mengembalikan JSON berisi:
    // - Daftar channel dalam kategori tersebut
    // - Channel yang aktif/berfungsi (working channel)
    echo getChannelsByCategory($_GET['category']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Category parameter is required']);
}
