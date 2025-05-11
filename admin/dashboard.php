<?php
require_once '../includes/functions.php';

// Tambahkan ini di paling awal file
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . SITE_URL . '/includes/login.php');
    exit;
}

requireAdmin();

$category = isset($_GET['category']) ? $_GET['category'] : 'news';
$channels = getChannels($category);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/media/Dex.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Admin Dashboard</h1>
            <div class="category-nav">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'news' ? 'active' : ''; ?>" href="?category=news">
                            <i class="fas fa-newspaper"></i> NEWS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'sport' ? 'active' : ''; ?>" href="?category=sport">
                            <i class="fas fa-running"></i> SPORT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'cartoon' ? 'active' : ''; ?>" href="?category=cartoon">
                            <i class="fas fa-child"></i> CARTOON
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'lokal' ? 'active' : ''; ?>" href="?category=lokal">
                            <i class="fas fa-tv"></i> LOKAL
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'event' ? 'active' : ''; ?>" href="?category=event">
                            <i class="fas fa-calendar-alt"></i> EVENT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'chat' ? 'active' : ''; ?>" href="?category=chat">
                            <i class="fas fa-comments"></i> MESSAGES
                            <span class="badge bg-danger unread-count"></span>
                        </a>
                    </li>
                    <li class="nav-item ms-auto">
                        <a class="nav-link text-danger" href="<?php echo SITE_URL; ?>/includes/logout.php">
                            <i class="fas fa-sign-out-alt"></i> LOGOUT
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <?php if ($category === 'chat'): ?>
        <div class="admin-content">
            <div class="content-header">
                <h2 class="category-title">USER MESSAGES</h2>
            </div>
            <div class="table-container">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Message</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="chatMessages">
                        <?php
                        $chatData = json_decode(file_get_contents(SITE_ROOT . '/data/chat_data.json'), true);
                        foreach ($chatData['messages'] as $message):
                        ?>
                        <tr data-id="<?php echo $message['id']; ?>">
                            <td><?php echo htmlspecialchars($message['username']); ?></td>
                            <td><?php echo htmlspecialchars($message['message']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($message['timestamp'])); ?></td>
                            <td>
                                <button class="btn btn-sm <?php echo $message['read'] ? 'btn-success' : 'btn-warning'; ?> toggle-read">
                                    <?php echo $message['read'] ? 'Read' : 'Unread'; ?>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger delete-message">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        document.querySelectorAll('.delete-message').forEach(button => {
            button.addEventListener('click', async function() {
                const row = this.closest('tr');
                const messageId = row.dataset.id;
                
                showConfirmDialog({
                    title: 'Delete Message',
                    message: 'Are you sure you want to delete this message?',
                    onConfirm: async () => {
                        try {
                            const response = await fetch(`/DexTV/admin/api/chat.php?id=${messageId}`, {
                                method: 'DELETE'
                            });
                            const data = await response.json();
                            
                            if (data.success) {
                                row.remove();
                                showAlert('Message deleted successfully', 'success');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showAlert('Failed to delete message', 'error');
                        }
                    }
                });
            });
        });

        document.querySelectorAll('.toggle-read').forEach(button => {
            button.addEventListener('click', async function() {
                const row = this.closest('tr');
                const messageId = row.dataset.id;
                
                try {
                    const response = await fetch(`/DexTV/admin/api/chat.php?id=${messageId}`, {
                        method: 'PUT'
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        button.classList.remove('btn-warning');
                        button.classList.add('btn-success');
                        button.textContent = 'Read';
                        showAlert('Message marked as read', 'success');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Failed to update message', 'error');
                }
            });
        });
        </script>
        <?php else: ?>
        <div class="admin-content">
            <div class="content-header">
                <h2 class="category-title"><?php echo strtoupper($category); ?> CHANNELS</h2>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addChannelModal">
                    <i class="fas fa-plus"></i> Add Channel
                </button>
            </div>
            
            <div class="table-container">
                <table class="table table-dark table-striped">
                    <colgroup>
                        <col width="10%"> <!-- ID -->
                        <col width="15%"> <!-- Name -->
                        <col width="35%"> <!-- URL -->
                        <col width="15%"> <!-- URL Status -->
                        <col width="25%"> <!-- Channel Status -->
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-nowrap">ID</th>
                            <th class="text-nowrap">Name</th>
                            <th class="text-nowrap">URL</th>
                            <th class="text-nowrap text-center">URL Status</th>
                            <th class="text-nowrap text-center">Channel Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($channels as $channel): ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($channel['id']); ?></td>
                            <td class="text-truncate"><?php echo htmlspecialchars($channel['name']); ?></td>
                            <td class="text-truncate"><?php echo htmlspecialchars($channel['url']); ?></td>
                            <td class="text-center">
                                <span class="url-status" data-url="<?php echo htmlspecialchars($channel['url']); ?>">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm visibility-toggle <?php echo $channel['status'] === 'active' ? 'btn-success' : 'btn-danger'; ?>"
                                            data-id="<?php echo $channel['id']; ?>" 
                                            data-status="<?php echo $channel['status']; ?>"
                                            title="<?php echo $channel['status'] === 'active' ? 'Channel Active' : 'Channel Inactive'; ?>">
                                        <i class="fas <?php echo $channel['status'] === 'active' ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                        <?php echo $channel['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-channel" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editChannelModal"
                                            data-id="<?php echo $channel['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($channel['name']); ?>"
                                            data-url="<?php echo htmlspecialchars($channel['url']); ?>"
                                            data-logo="<?php echo htmlspecialchars($channel['logo']); ?>"
                                            data-status="<?php echo $channel['status']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-channel" data-id="<?php echo $channel['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'modals/channel_modal.php'; ?>
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
        
        // Edit channel button handler
        document.querySelectorAll('.edit-channel').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = document.getElementById('editChannelForm');
                form.querySelector('[name="id"]').value = this.dataset.id;
                form.querySelector('[name="name"]').value = this.dataset.name;
                form.querySelector('[name="url"]').value = this.dataset.url;
                form.querySelector('[name="logo"]').value = this.dataset.logo;
                form.querySelector('[name="status"]').value = this.dataset.status;
            });
        });

        // Check URLs periodically
        async function checkAllUrls() {
            const statusElements = document.querySelectorAll('.url-status');
            const urls = Array.from(statusElements).map(el => el.dataset.url);
            
            try {
                const response = await fetch('/DexTV/admin/api/check_url.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ urls: urls })
                });
                
                const data = await response.json();
                if (data.success) {
                    statusElements.forEach(element => {
                        const url = element.dataset.url;
                        const result = data.results[url];
                        updateUrlStatus(element, result.isOnline);
                    });
                }
            } catch (error) {
                console.error('Failed to check URLs:', error);
            }
        }

        // Update URL status display
        function updateUrlStatus(element, isOnline) {
            const icon = isOnline ? 
                '<i class="fas fa-circle text-success" title="URL is working"></i>' :
                '<i class="fas fa-circle text-danger" title="URL is not working"></i>';
            const text = isOnline ? 'Online' : 'Offline';
            
            element.innerHTML = `${icon} ${text}`;
            element.classList.remove('text-success', 'text-danger');
            element.classList.add(isOnline ? 'text-success' : 'text-danger');
        }

        // Initial check
        checkAllUrls();
        
        // Set interval for periodic checks (every 30 seconds)
        setInterval(checkAllUrls, 30000);

        // Toggle channel visibility handler
        document.querySelectorAll('.visibility-toggle').forEach(btn => {
            btn.addEventListener('click', async function() {
                const channelId = this.dataset.id;
                const currentStatus = this.dataset.status;
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                
                try {
                    const response = await fetch('/DexTV/admin/api/channel.php', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id: channelId,
                            status: newStatus
                        })
                    });
                    
                    if (response.ok) {
                        this.dataset.status = newStatus;
                        this.classList.remove('btn-success', 'btn-danger');
                        this.classList.add(newStatus === 'active' ? 'btn-success' : 'btn-danger');
                        this.innerHTML = `<i class="fas fa-eye${newStatus === 'active' ? '' : '-slash'}"></i> ${newStatus === 'active' ? 'Active' : 'Inactive'}`;
                        this.title = `Channel ${newStatus === 'active' ? 'Active' : 'Inactive'}`;
                        showAlert(`Channel is now ${newStatus}`, 'success');
                    }
                } catch (error) {
                    showAlert('Failed to update channel status', 'error');
                }
            });
        });
    });
    </script>
</body>
</html>
