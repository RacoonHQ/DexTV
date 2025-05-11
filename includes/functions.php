<?php
require_once 'config.php';

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
        'samesite' => 'Strict'
    ]);
    session_start();
}

// CSRF token generator & checker
function getCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function checkCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Escape output helper
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function getChannels($category, $activeOnly = false) {
    $jsonFile = SITE_ROOT . '/data/data_tv.json';
    clearstatcache(true, $jsonFile); // Clear file cache to get fresh data
    
    if (!file_exists($jsonFile)) {
        error_log("JSON file not found: $jsonFile");
        return [];
    }
    
    $jsonContent = file_get_contents($jsonFile);
    if ($jsonContent === false) {
        error_log("Could not read JSON file: $jsonFile");
        return [];
    }
    
    $jsonData = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return [];
    }

    // Get channels for specific category
    if (isset($jsonData['categories'][$category])) {
        $channels = $jsonData['categories'][$category]['channels'];
        
        // Filter inactive channels if activeOnly is true
        if ($activeOnly) {
            $channels = array_values(array_filter($channels, function($channel) {
                return isset($channel['status']) && $channel['status'] === 'active';
            }));
        }
        
        return $channels;
    }
    
    // Get all channels across categories
    if ($category === 'all') {
        $allChannels = [];
        foreach ($jsonData['categories'] as $cat) {
            if (isset($cat['channels'])) {
                if ($activeOnly) {
                    // Filter inactive channels
                    $activeChannels = array_filter($cat['channels'], function($channel) {
                        return isset($channel['status']) && $channel['status'] === 'active';
                    });
                    $allChannels = array_merge($allChannels, array_values($activeChannels));
                } else {
                    $allChannels = array_merge($allChannels, $cat['channels']);
                }
            }
        }
        return $allChannels;
    }
    
    return [];
}

function testChannelAvailability($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode >= 200 && $httpCode < 400;
}

function findFirstWorkingChannel($category) {
    $channels = getChannels($category);
    
    foreach ($channels as $channel) {
        if (testChannelAvailability($channel['url'])) {
            return $channel;
        }
    }
    
    return null;
}

function getChannelsByCategory($category) {
    header('Content-Type: application/json');
    $workingChannel = findFirstWorkingChannel($category);
    return json_encode([
        'channels' => getChannels($category),
        'activeChannel' => $workingChannel
    ]);
}

function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $page) ? 'active' : '';
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['last_page'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . SITE_URL . '/includes/login.php');
        exit;
    }
}

function redirectToLastPage() {
    if (isset($_SESSION['last_page'])) {
        $lastPage = $_SESSION['last_page'];
        unset($_SESSION['last_page']);
        return $lastPage;
    }
    return SITE_URL . '/index.php';
}

function checkVideoPlayback(string $url): bool {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($httpCode >= 200 && $httpCode < 300);
}

function getNextChannel(array $channels, int $currentIndex): ?array {
    if ($currentIndex + 1 < count($channels)) {
        return $channels[$currentIndex + 1];
    }
    return null;
}

function getLastChannel($category) {
    $channels = getChannels($category);
    return end($channels); // Returns the last channel in array
}

function saveChannels($channels) {
    $jsonFile = SITE_ROOT . '/data/data_tv.json';
    $jsonData = json_decode(file_get_contents($jsonFile), true);
    $jsonData['categories'] = $channels;
    return file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
}

function addChannel($data) {
    try {
        $jsonFile = SITE_ROOT . '/data/data_tv.json';
        // Pastikan file ada dan bisa dibaca
        if (!file_exists($jsonFile)) {
            // Buat struktur dasar jika file belum ada
            $jsonData = ['categories' => []];
        } else {
            $jsonContent = file_get_contents($jsonFile);
            $jsonData = json_decode($jsonContent, true);
            if (!is_array($jsonData)) {
                $jsonData = ['categories' => []];
            }
        }

        $category = $data['category'];

        // Pastikan kategori ada
        if (!isset($jsonData['categories'][$category])) {
            $jsonData['categories'][$category] = [
                'channels' => []
            ];
        }

        // Get current channels and find next ID number
        $channels = $jsonData['categories'][$category]['channels'];
        $nextId = 1;

        // Find the highest existing ID number
        foreach ($channels as $channel) {
            if (preg_match('/' . preg_quote($category, '/') . '_(\d+)/', $channel['id'], $matches)) {
                $currentId = (int)$matches[1];
                $nextId = max($nextId, $currentId + 1);
            }
        }

        // Create new channel with sequential ID
        $newChannel = [
            'id' => $category . '_' . $nextId,
            'name' => $data['name'],
            'url' => $data['url'],
            'logo' => !empty($data['logo']) ? $data['logo'] : strtolower($data['name']) . '.png',
            'status' => $data['status'] ?? 'active'
        ];

        $jsonData['categories'][$category]['channels'][] = $newChannel;

        return file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        error_log('Error adding channel: ' . $e->getMessage());
        return false;
    }
}

function updateChannel($data) {
    try {
        if (!isset($data['id']) || !isset($data['category'])) {
            error_log('Missing required data: ' . json_encode($data));
            return false;
        }

        $jsonFile = SITE_ROOT . '/data/data_tv.json';
        $jsonData = json_decode(file_get_contents($jsonFile), true);
        
        $category = $data['category'];
        $channelId = $data['id'];
        
        if (!isset($jsonData['categories'][$category])) {
            error_log("Category not found: $category");
            return false;
        }

        $updated = false;
        foreach ($jsonData['categories'][$category]['channels'] as &$channel) {
            if ($channel['id'] === $channelId) {
                $channel['name'] = $data['name'];
                $channel['url'] = $data['url'];
                $channel['logo'] = !empty($data['logo']) ? $data['logo'] : $channel['logo'];
                $channel['status'] = $data['status'] ?? $channel['status'];
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            error_log("Channel not found: $channelId in category $category");
            return false;
        }

        $result = file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
        return $result !== false;

    } catch (Exception $e) {
        error_log('Error updating channel: ' . $e->getMessage());
        return false;
    }
}

function deleteChannel($id) {
    try {
        $jsonFile = SITE_ROOT . '/data/data_tv.json';
        $jsonData = json_decode(file_get_contents($jsonFile), true);
        
        // Find category and channel
        foreach ($jsonData['categories'] as $category => &$categoryData) {
            // Convert to indexed array to make reindexing easier
            $channels = array_values($categoryData['channels']);
            $found = false;
            
            // Find and remove the channel
            foreach ($channels as $index => $channel) {
                if ($channel['id'] === $id) {
                    unset($channels[$index]);
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                // Reindex remaining channels
                $channels = array_values($channels);
                // Update IDs sequentially
                foreach ($channels as $index => &$channel) {
                    $channel['id'] = $category . '_' . ($index + 1);
                }
                $categoryData['channels'] = $channels;
                break;
            }
        }
        
        return file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        error_log('Error deleting channel: ' . $e->getMessage());
        return false;
    }
}

function renderFirstChannelPlayer($category) {
    $channels = getChannels($category, true);
    if (count($channels) === 0) return;
    $first = $channels[0];
    ?>
    <div class="video-player mb-4">
        <video id="mainPlayer" class="w-100" controls autoplay>
            <source src="<?php echo htmlspecialchars($first['url']); ?>" type="application/x-mpegURL">
            Your browser does not support the video tag.
        </video>
        <div class="mt-2 fw-bold"><?php echo htmlspecialchars($first['name']); ?></div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.play-channel').forEach(btn => {
            btn.addEventListener('click', function() {
                const player = document.getElementById('mainPlayer');
                player.src = this.dataset.url;
                player.play();
                document.querySelector('.video-player .fw-bold').textContent = this.dataset.name;
            });
        });
    });
    </script>
    <?php
}
?>
