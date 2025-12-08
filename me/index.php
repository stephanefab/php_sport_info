<?php
require_once __DIR__ . '/../functions.php';

if (!isLogged()) {
    redirectToLogin();
}

$user = getCurrentUser();
$pageTitle = 'Mon Profil - Sports Comments';

$errors = [];
$success = '';

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mise à jour des informations
    if (isset($_POST['update_profile'])) {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '', true);

        // Validation du nom
        if (empty($name)) {
            $errors[] = 'Le nom est requis.';
        } elseif (strlen($name) < 2 || strlen($name) > 150) {
            $errors[] = 'Le nom doit contenir entre 2 et 150 caractères.';
        }

        // Validation de l'email
        if (empty($email)) {
            $errors[] = 'L\'email est requis.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide.';
        } else {
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $existingUser = getOneByColumn('users', 'email', $email);
            if ($existingUser && $existingUser['id'] != $user['id']) {
                $errors[] = 'Cet email est déjà utilisé par un autre compte.';
            }
        }

        // Gestion de l'avatar
        $avatarPath = $user['path'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = checkUpload($_FILES['avatar']);
            if ($uploadResult) {
                $savedPath = saveUploadWithFolders($_FILES['avatar'], $uploadResult['name'], __DIR__ . '/../uploads');
                if ($savedPath) {
                    $avatarPath = '/uploads/' . date('Y/m/d') . '/' . $uploadResult['name'];
                }
            } else {
                $errors[] = 'Format d\'image invalide ou fichier trop volumineux (max 2MB).';
            }
        }

        if (empty($errors)) {
            $updated = update('users', $user['id'], [
                'name' => $name,
                'email' => $email,
                'path' => $avatarPath
            ]);

            if ($updated) {
                // Mettre à jour la session
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $user = getCurrentUser();
                $success = 'Profil mis à jour avec succès !';
            } else {
                $errors[] = 'Erreur lors de la mise à jour du profil.';
            }
        }
    }

    // Changement de mot de passe
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Vérifier le mot de passe actuel
        if (empty($currentPassword)) {
            $errors[] = 'Le mot de passe actuel est requis.';
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $errors[] = 'Le mot de passe actuel est incorrect.';
        }

        // Validation du nouveau mot de passe
        if (empty($newPassword)) {
            $errors[] = 'Le nouveau mot de passe est requis.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
        }

        // Vérifier la confirmation
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (empty($errors)) {
            $updated = updatePassword($user['id'], $newPassword);
            if ($updated) {
                $success = 'Mot de passe modifié avec succès !';
            } else {
                $errors[] = 'Erreur lors du changement de mot de passe.';
            }
        }
    }
}

// Recharger les données utilisateur
$user = getCurrentUser();
?>

<?php include_once __DIR__ . '/include/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="bi bi-person-circle"></i> Mon Profil
        </h1>
    </div>
</div>

<!-- Messages d'erreur ou de succès -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Carte de profil -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <?php if ($user['path']): ?>
                    <img src="<?= htmlspecialchars($user['path']) ?>" alt="Avatar"
                         class="rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 150px; height: 150px;">
                        <span class="display-1 text-white"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                    </div>
                <?php endif; ?>

                <h3 class="mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>

                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?> fs-6">
                    <i class="bi bi-<?= $user['role'] === 'admin' ? 'shield-lock' : 'person' ?>"></i>
                    <?= $user['role'] === 'admin' ? 'Administrateur' : 'Utilisateur' ?>
                </span>

                <hr class="my-4">

                <div class="text-start">
                    <p class="mb-2">
                        <i class="bi bi-calendar3 text-muted me-2"></i>
                        <strong>Inscrit le :</strong><br>
                        <span class="ms-4"><?= date('d/m/Y à H:i', strtotime($user['created_at'])) ?></span>
                    </p>
                    <?php if ($user['updated_at']): ?>
                    <p class="mb-0">
                        <i class="bi bi-pencil-square text-muted me-2"></i>
                        <strong>Dernière modification :</strong><br>
                        <span class="ms-4"><?= date('d/m/Y à H:i', strtotime($user['updated_at'])) ?></span>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0"><i class="bi bi-lightning-fill text-warning"></i> Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/index.php" class="btn btn-outline-primary">
                        <i class="bi bi-house"></i> Retour à l'accueil
                    </a>
                    <?php if ($user['role'] === 'admin'): ?>
                    <a href="/admin" class="btn btn-outline-danger">
                        <i class="bi bi-speedometer2"></i> Dashboard Admin
                    </a>
                    <?php endif; ?>
                    <a href="/logout.php" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-right"></i> Se déconnecter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaires de modification -->
    <div class="col-lg-8">
        <!-- Modifier les informations -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Modifier mes informations</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_profile" value="1">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?= htmlspecialchars($user['name']) ?>" required
                                       minlength="2" maxlength="150">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="avatar" class="form-label">Photo de profil</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="file" class="form-control" id="avatar" name="avatar"
                                       accept="image/jpeg,image/png,image/gif">
                            </div>
                            <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2 MB</div>
                        </div>

                        <!-- Prévisualisation de l'image -->
                        <div class="col-12">
                            <div id="preview-container" class="d-none">
                                <label class="form-label">Prévisualisation :</label>
                                <div>
                                    <img id="avatar-preview" src="" alt="Prévisualisation"
                                         class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Changer le mot de passe -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Changer mon mot de passe</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="current_password"
                                       name="current_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="new_password"
                                       name="new_password" required minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Minimum 6 caractères</div>
                        </div>

                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password"
                                       name="confirm_password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Indicateur de force du mot de passe -->
                    <div class="mt-3">
                        <label class="form-label small">Force du mot de passe :</label>
                        <div class="progress" style="height: 5px;">
                            <div id="password-strength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="password-strength-text" class="text-muted"></small>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Prévisualisation de l'avatar
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewContainer = document.getElementById('preview-container');
    const preview = document.getElementById('avatar-preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.classList.add('d-none');
    }
});

// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
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

// Indicateur de force du mot de passe
document.getElementById('new_password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');

    let strength = 0;
    let text = '';
    let colorClass = '';

    if (password.length >= 6) strength += 25;
    if (password.length >= 10) strength += 25;
    if (/[A-Z]/.test(password)) strength += 15;
    if (/[a-z]/.test(password)) strength += 10;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 10;

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
});

// Validation de confirmation de mot de passe
document.getElementById('confirm_password').addEventListener('input', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = e.target.value;

    if (confirmPassword.length > 0 && newPassword !== confirmPassword) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
});
</script>

<?php include_once __DIR__ . '/include/footer.php'; ?>
