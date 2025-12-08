<?php
require_once __DIR__ . '/functions.php';
$pageTitle = "Inscription";

// Rediriger si déjà connecté
if (isLogged()) {
    redirectToHome();
}

if (!empty($_POST)) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '', true);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (!$password || !$email || !$name) {
        setFlashMessage("Tous les champs sont requis", 'error');
    }

    if (strlen($name) < 2 || strlen($name) > 100) {
        setFlashMessage("Le nom doit contenir entre 2 et 100 caracteres", 'error');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage("L'email n'est pas valide", 'error');
    }

    if (strlen($email) > 150) {
        setFlashMessage("L'email est trop long", 'error');
    }

    if (strlen($password) < 6) {
        setFlashMessage("Le mot de passe doit contenir au moins 6 caracteres", 'error');
    }

    if ($password !== $password_confirm) {
        setFlashMessage("Les mots de passe ne correspondent pas", 'error');
    }

    if (getOneByColumn("users", "email", $email)) {
        setFlashMessage("Cet email est deja utilise", 'error');
    }

    // Si pas d'erreurs de validation
    if (!hasErrors()) {
        if (createUser($name, $email, $password)) {
            login($email, $password);
            setFlashMessage("Inscription reussie ! Bienvenue sur SportsComments.", 'success');
            redirectToHome();
        } else {
            setFlashMessage("Une erreur est survenue, veuillez reessayer", 'error');
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

        .register-container {
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }

        .register-header .logo {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .register-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .register-header p {
            opacity: 0.9;
            margin: 0;
            font-size: 0.875rem;
        }

        .register-body {
            padding: 2rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #f39c12;
            box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.15);
        }

        .btn-register {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: none;
            border-radius: 10px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(243, 156, 18, 0.4);
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

        .register-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
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

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        .strength-text {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .form-text {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="back-home">
    <a href="/index.php">
        <i class="bi bi-arrow-left"></i> Retour a l'accueil
    </a>
</div>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="logo">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <h1>Creer un compte</h1>
            <p>Rejoignez la communaute SportsComments</p>
        </div>

        <div class="register-body">
            <?php displayFlash(); ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        <i class="bi bi-person me-1"></i> Nom complet
                    </label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Votre nom" value="<?= htmlspecialchars($name ?? '') ?>"
                           required minlength="2" maxlength="100">
                </div>

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
                               placeholder="Minimum 6 caracteres" required minlength="6">
                        <button type="button" class="password-toggle" data-target="password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="progress password-strength" style="height: 4px;">
                        <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small id="password-strength-text" class="strength-text text-muted"></small>
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label fw-semibold">
                        <i class="bi bi-lock-fill me-1"></i> Confirmer le mot de passe
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                               placeholder="Retapez votre mot de passe" required>
                        <button type="button" class="password-toggle" data-target="password_confirm">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small id="password-match" class="form-text"></small>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-warning btn-register text-white">
                        <i class="bi bi-person-plus me-2"></i> Creer mon compte
                    </button>
                </div>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="text-center">
                <p class="text-muted mb-0">Deja un compte ?</p>
                <a href="/login.php" class="btn btn-outline-secondary mt-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                </a>
            </div>
        </div>

        <div class="register-footer">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Vos donnees sont securisees
            </small>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.querySelectorAll('.password-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
});

// Password strength indicator
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    let strength = 0;
    if (password.length >= 6) strength += 25;
    if (password.length >= 10) strength += 25;
    if (/[A-Z]/.test(password)) strength += 15;
    if (/[a-z]/.test(password)) strength += 10;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 10;

    let text = '', colorClass = '';
    if (strength < 30) {
        text = 'Faible';
        colorClass = 'bg-danger';
    } else if (strength < 60) {
        text = 'Moyen';
        colorClass = 'bg-warning';
    } else if (strength < 80) {
        text = 'Bon';
        colorClass = 'bg-info';
    } else {
        text = 'Excellent';
        colorClass = 'bg-success';
    }

    strengthBar.style.width = strength + '%';
    strengthBar.className = 'progress-bar ' + colorClass;
    strengthText.textContent = password.length > 0 ? text : '';
    strengthText.className = 'strength-text text-' + (colorClass.replace('bg-', ''));
});

// Password match validation
document.getElementById('password_confirm').addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = e.target.value;
    const matchText = document.getElementById('password-match');

    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            matchText.textContent = 'Les mots de passe correspondent';
            matchText.className = 'form-text text-success';
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else {
            matchText.textContent = 'Les mots de passe ne correspondent pas';
            matchText.className = 'form-text text-danger';
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        }
    } else {
        matchText.textContent = '';
        e.target.classList.remove('is-valid', 'is-invalid');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
