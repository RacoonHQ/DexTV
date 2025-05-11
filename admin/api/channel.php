<?php
require_once '../../includes/functions.php';
requireAdmin();

header('Content-Type: application/json');

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
        case 'PUT':
        case 'DELETE':
            // CSRF protection
            $headers = getallheaders();
            $csrfToken = $headers['X-CSRF-Token'] ?? '';
            if (!checkCsrfToken($csrfToken)) {
                throw new Exception('Invalid CSRF token');
            }
            break;
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!validateChannelData($data)) {
                throw new Exception('Invalid channel data');
            }
            $result = addChannel($data);
            echo json_encode(['success' => $result !== false]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception('Channel ID is required');
            }

            // Log untuk debugging
            error_log('Updating channel: ' . json_encode($data));
            
            $result = updateChannel($data);
            if (!$result) {
                throw new Exception('Failed to update channel');
            }
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('Channel ID is required');
            }
            $result = deleteChannel($_GET['id']);
            echo json_encode(['success' => $result !== false]);
            break;
            
        default:
            throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function validateChannelData($data, $isUpdate = false) {
    if ($isUpdate && !isset($data['id'])) {
        error_log("Update validation failed: Missing ID");
        return false;
    }
    
    if (!isset($data['name']) || trim($data['name']) === '' || 
        !isset($data['url']) || trim($data['url']) === '') {
        error_log("Validation failed: Missing name or URL");
        return false;
    }
    
    if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
        error_log("Validation failed: Invalid URL format");
        return false;
    }
    
    return true;
}

function updateChannelStatus($channelId, $status) {
    $jsonFile = SITE_ROOT . '/data/data_tv.json';
    $jsonData = json_decode(file_get_contents($jsonFile), true);
    
    foreach ($jsonData['categories'] as &$category) {
        foreach ($category['channels'] as &$channel) {
            if ($channel['id'] === $channelId) {
                $channel['status'] = $status;
                return file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
            }
        }
    }
    return false;
}
?>
