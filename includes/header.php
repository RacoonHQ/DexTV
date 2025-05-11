<?php
// Tambahkan ini di awal file sebelum HTML
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Nonton TV Online</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/media/Dex.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/styles.css">
    
    <?php include 'alert.php'; ?>
    
    <script>
    async function getChannels(category) {
        try {
            const response = await fetch(`<?php echo SITE_URL; ?>/api/channels.php?category=${category}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching channels:', error);
            return [];
        }
    }

    function playLastChannel(category) {
        fetch(`<?php echo SITE_URL; ?>/api/channels.php?category=${category}`)
            .then(response => response.json())
            .then(channels => {
                if (channels.length > 0) {
                    const lastChannel = channels[channels.length - 1];
                    if (window.playChannel) {
                        window.playChannel(lastChannel.url);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Add click handlers to menu items
    document.addEventListener('DOMContentLoaded', () => {
        const menuItems = document.querySelectorAll('nav a');
        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                const href = item.getAttribute('href');
                if (href.includes('news.php') || href.includes('sport.php') || 
                    href.includes('cartoon.php') || href.includes('lokal.php') || 
                    href.includes('event.php')) {
                    const category = href.replace('.php', '').replace('/DexTV/', '');
                    playLastChannel(category);
                }
            });
        });
    });
    </script>
    <script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.href = '<?php echo SITE_URL; ?>/index.php';
        }
    });
    </script>
</head>
<body>
    <header class="main-header" style="background: var(--gradient-dark); padding: 1rem 2rem;">
        <div class="d-flex align-items-center">
            <img src="<?php echo SITE_URL; ?>/media/Dex.png" alt="DexTV Logo" style="height:32px;width:auto;margin-right:10px;">
            <span class="fw-bold" style="font-size:1.5rem;letter-spacing:0.05em;color:#fff;">DEXTV</span>
        </div>
        <nav>
            <ul>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo SITE_URL; ?>/index.php" class="<?php echo isActivePage('index.php'); ?><?php echo isActivePage('index.php') ? ' active' : ''; ?>">
                        <i class="fas fa-home"></i> BERANDA
                    </a></li>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo SITE_URL; ?>/sport.php" class="<?php echo isActivePage('sport.php'); ?><?php echo isActivePage('sport.php') ? ' active' : ''; ?>">
                        <i class="fas fa-running"></i> SPORT
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/news.php" class="<?php echo isActivePage('news.php'); ?><?php echo isActivePage('news.php') ? ' active' : ''; ?>">
                        <i class="fas fa-newspaper"></i> NEWS
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/cartoon.php" class="<?php echo isActivePage('cartoon.php'); ?><?php echo isActivePage('cartoon.php') ? ' active' : ''; ?>">
                        <i class="fas fa-child"></i> CARTOON
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/lokal.php" class="<?php echo isActivePage('lokal.php'); ?><?php echo isActivePage('lokal.php') ? ' active' : ''; ?>">
                        <i class="fas fa-tv"></i> LOKAL
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/event.php" class="<?php echo isActivePage('event.php'); ?><?php echo isActivePage('event.php') ? ' active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i> EVENT
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/includes/logout.php">
                        <i class="fas fa-sign-out-alt"></i> LOGOUT
                    </a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>/includes/login.php" class="<?php echo isActivePage('login.php'); ?><?php echo isActivePage('login.php') ? ' active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> LOGIN
                    </a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
    </header>
</body>
</html>
