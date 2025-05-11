<?php
require_once '../../includes/functions.php';
requireAdmin();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Accept array of URLs or single URL
    $urls = isset($data['urls']) ? $data['urls'] : (isset($data['url']) ? [$data['url']] : []);
    
    if (empty($urls)) {
        throw new Exception('URLs are required');
    }

    $results = [];
    $mh = curl_multi_init();
    $channels = [];

    // Setup curl handles
    foreach ($urls as $index => $url) {
        $channels[$index] = curl_init($url);
        curl_setopt_array($channels[$index], [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        curl_multi_add_handle($mh, $channels[$index]);
    }

    // Execute all requests simultaneously
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running);

    // Get all the results
    foreach ($channels as $index => $ch) {
        $error = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $results[$urls[$index]] = [
            'isOnline' => !$error && $httpCode >= 200 && $httpCode < 400,
            'httpCode' => $httpCode
        ];
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);

    echo json_encode([
        'success' => true,
        'results' => $results
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
