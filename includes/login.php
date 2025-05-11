<?php
require_once 'config.php';
require_once 'functions.php';



// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . SITE_URL . '/sport.php');
    }
    exit;
}

// Mencegah akses kembali ke halaman login
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Load user data dari JSON
    $users = json_decode(file_get_contents(SITE_ROOT . '/data/user_data.json'), true)['users'];
    
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Set session history untuk redirect
            $_SESSION['last_page'] = SITE_URL . '/index.php';
            
            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header('Location: ' . SITE_URL . '/admin/dashboard.php');
            } else {
                header('Location: ' . SITE_URL . '/sport.php');
            }
            exit;
        }
    }
    $error = "Invalid username or password";
}

include 'header.php';
?>

<head>
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/media/Dex.png">
    <!-- ...existing code... -->
</head>

<body class="page-with-video">
    <div class="bg-video-wrapper">
        <video class="bg-video" autoplay muted loop playsinline>
            <source src="<?php echo SITE_URL; ?>/media/background.mp4" type="video/mp4">
        </video>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="auth-card">
                    <h3 class="text-center mb-4">Login</h3>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control custom-input" required>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control custom-input" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Login</button>
                        <div class="text-center">
                            <span class="text-secondary">Belum punya akun?</span>
                            <a href="register.php" class="text-accent">Register</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('input');
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});
</script>

<link rel="stylesheet" href="styles.css">

<?php include 'footer.php'; ?>
