<?php 
require_once 'includes/functions.php';
include 'includes/header.php';
?>

<head>
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/media/Dex.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>

<body class="page-with-video">
    <div class="bg-video-wrapper">
        <video class="bg-video" autoplay muted loop playsinline>
            <source src="<?php echo SITE_URL; ?>/media/background.mp4" type="video/mp4">
        </video>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4">Selamat Datang di DexTV</h1>
                <p class="lead mb-5 text-secondary">Platform streaming TV online terbaik dengan berbagai pilihan channel lokal dan internasional.</p>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="fas fa-tv fa-3x mb-3"></i>
                            <h5>Channel Berkualitas</h5>
                            <p>Nikmati berbagai channel TV berkualitas HD.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="fas fa-globe fa-3x mb-3"></i>
                            <h5>Akses Global</h5>
                            <p>Tonton channel dari berbagai negara.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="fas fa-clock fa-3x mb-3"></i>
                            <h5>24/7 Streaming</h5>
                            <p>Streaming non-stop 24 jam sehari.</p>
                        </div>
                    </div>
                </div>

                <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="mt-5">
                    <a href="includes/login.php" class="btn btn-primary btn-lg mx-2">Login</a>
                    <a href="includes/register.php" class="btn btn-outline-light btn-lg mx-2">Register</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

<style>
.feature-card {
    background: var(--gradient-card);
    padding: 2rem;
    border-radius: 10px;
    border: 1px solid var(--color-border);
    transition: all 0.3s ease;
    color: var(--color-text);
    box-shadow: var(--shadow-soft);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-strong);
    background: var(--gradient-secondary);
}

.feature-card i {
    color: var(--color-accent);
}

.feature-card h5 {
    color: var(--color-text);
    margin: 1rem 0;
}

.feature-card p {
    color: var(--color-text-secondary);
}

.display-4 {
    color: var(--color-text);
    font-weight: 600;
}

.lead {
    color: var(--color-text-secondary);
}

.btn-primary {
    background: var(--gradient-accent);
    border: none;
    box-shadow: var(--shadow-soft);
}

.btn-primary:hover {
    background: var(--gradient-hover);
    box-shadow: var(--shadow-strong), var(--glow-soft);
    transform: translateY(-2px);
}

.btn-outline-light {
    border: 1px solid var(--color-border);
    color: var(--color-text);
}

.btn-outline-light:hover {
    background: var(--gradient-secondary);
    border-color: var(--color-accent);
    color: var(--color-text);
    transform: translateY(-2px);
}
</style>

<?php 
$category = isset($_GET['category']) ? $_GET['category'] : 'news';
// Pastikan selalu menggunakan activeOnly = true untuk tampilan user
$channels = getChannels($category, true);

// Debug: cek jumlah channel aktif (opsional, bisa dihapus nanti)
error_log("Active channels for $category: " . count($channels));
?>

<?php include 'includes/footer.php'; ?>
