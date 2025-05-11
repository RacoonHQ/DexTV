<?php
require_once '../../includes/functions.php';
requireAdmin();

header('Content-Type: application/json');

$chatFile = SITE_ROOT . '/data/chat_data.json';
$chatData = json_decode(file_get_contents($chatFile), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not logged in']);
            exit;
        }

        $newMessage = [
            'id' => uniqid(),
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'message' => $input['message'],
            'timestamp' => $input['timestamp'],
            'read' => false
        ];
        
        $chatData['messages'][] = $newMessage;
        
        if (file_put_contents($chatFile, json_encode($chatData, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save message']);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Message ID required']);
            exit;
        }

        $messageId = $_GET['id'];
        $messages = $chatData['messages'];
        $chatData['messages'] = array_filter($messages, function($msg) use ($messageId) {
            return $msg['id'] !== $messageId;
        });
        
        if (file_put_contents($chatFile, json_encode($chatData, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete message']);
        }
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Message ID required']);
            exit;
        }

        $messageId = $_GET['id'];
        foreach ($chatData['messages'] as &$message) {
            if ($message['id'] === $messageId) {
                $message['read'] = true;
                break;
            }
        }

        if (file_put_contents($chatFile, json_encode($chatData, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update message']);
        }
        break;
}
