<?php
require_once __DIR__ . '/functions.php';
$pageTitle = "S'inscrire";


if(!empty($_POST)){
$name = sanitizeInput($_POST['name'] ?? '', 1);
$email = sanitizeInput($_POST['email'] ?? '', 1);
$password = sanitizeInput($_POST['password'] ?? '');

// Validation
if(!$password || !$email || !$name){
    setFlashMessage("Tous les champs sont requis", 'error');
}

if(strlen($name) < 3 || strlen($name) > 100){
    setFlashMessage("Le nom doit être compris entre 3 et 100 caractères", 'error');
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100){
    setFlashMessage("L'email n'est pas correct", 'error');
}

if(strlen($email) > 100){
    setFlashMessage("L'email doit contenir au maximum 100 caractères", 'error');
}

if(strlen($password) < 6){
    setFlashMessage("Le mot de passe doit contenir au moins 6 caractères", 'error');
}

if(getOneByColumn("users", "email", $email)){
    setFlashMessage("L'email est déjà utilisé", 'error');
}

// Si pas d'erreurs de validation
if(!hasErrors()){
    if(createUser($name, $email, $password)){
        setFlashMessage("Inscription réussie", 'success');
        login($email, $password);
        redirectToHome();
    } else {
        setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
    }
}
}
?>
<?php include_once __DIR__ . '/include/header.php'; ?>
<div>
    <h3><?= $pageTitle ?></h3>
    <form action="" method="post" enctype="multipart/form-data">
        <?php displayFlash(); ?>
         <!-- Input texte -->
        <div class="mb-3">
            <label for="name" class="form-label">name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Entrez votre nom" value="<?= $name ?? ''; ?>">
        </div>

        <!-- Input texte -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre nom" value="<?= $email ?? ''; ?>">
        </div>

        <!-- Password avec toggle -->
        <div class="mb-3 position-relative">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Entrez votre mot de passe">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
        <p>Déjà un compte? Connectez vous <a href="/login.php">Ici</a></p>
    </form>

</div>
<!-- Script pour toggle password -->
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Changer l'icône
        togglePassword.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
</script>
<?php include_once __DIR__ . '/include/footer.php'; ?>