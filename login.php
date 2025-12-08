<?php
require_once __DIR__ . '/functions.php';
$pageTitle = "Connexion";

// Rediriger si déjà connecté
if (isLogged()) {
    redirectToHome();
}

if (!empty($_POST)) {
    $email = sanitizeInput($_POST['email'] ?? '', true);
    $password = $_POST['password'] ?? '';

    // Validation
    if (!$password || !$email) {
        setFlashMessage("Tous les champs sont requis", 'error');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage("L'email n'est pas valide", 'error');
    }

    // Si pas d'erreurs de validation
    if (!hasErrors()) {
        if (login($email, $password)) {
            setFlashMessage("Connexion reussie ! Bienvenue.", 'success');
            redirectToHome();
        } else {
            setFlashMessage("Email ou mot de passe incorrect", 'error');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Sports Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1d21 0%, #2d3238 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: #fff;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .login-header .logo {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .login-header p {
            opacity: 0.9;
            margin: 0;
            font-size: 0.9rem;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            height: 58px;
            padding: 1rem 0.75rem;
        }

        .form-floating .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
        }

        .form-floating label {
            padding: 1rem 0.75rem;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #3498db;
        }

        .btn-login {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 10px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(52, 152, 219, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #adb5bd;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e9ecef;
        }

        .divider span {
            padding: 0 1rem;
            font-size: 0.875rem;
        }

        .login-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .login-footer a {
            color: #3498db;
            font-weight: 500;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .back-home a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-home a:hover {
            color: #fff;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }

        .password-wrapper {
            position: relative;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>

<div class="back-home">
    <a href="/index.php">
        <i class="bi bi-arrow-left"></i> Retour a l'accueil
    </a>
</div>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <h1>Connexion</h1>
            <p>Accedez a votre compte SportsComments</p>
        </div>

        <div class="login-body">
            <?php displayFlash(); ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        <i class="bi bi-envelope me-1"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="votre@email.com" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        <i class="bi bi-lock me-1"></i> Mot de passe
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Votre mot de passe" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
                    </button>
                </div>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="text-center">
                <p class="text-muted mb-0">Pas encore de compte ?</p>
                <a href="/register.php" class="btn btn-outline-secondary mt-2">
                    <i class="bi bi-person-plus me-1"></i> Creer un compte
                </a>
            </div>
        </div>

        <div class="login-footer">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Connexion securisee
            </small>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
