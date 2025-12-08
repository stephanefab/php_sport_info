<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

if (isset($_POST['submit_form'])) {
    $name = sanitizeInput($_POST['name'] ?? '', 1);
    $description = sanitizeInput($_POST['description'] ?? '');
    $path = sanitizeInput($_POST['path'] ?? '');

    if (!$name || !$description) {
        setFlashMessage("Tous les champs sont requis", 'error');
    }

    if (strlen($name) < 3 || strlen($name) > 100) {
        setFlashMessage("Le nom doit être compris entre 3 et 100 caractères", 'error');
    }

    if (strlen($description) < 6 || strlen($description) > 100) {
        setFlashMessage("La description doit contenir entre 6 et 100 caractères", 'error');
    }

    if (!empty($path) && !filter_var($path, FILTER_VALIDATE_URL)) {
        setFlashMessage("L'url de l'image n'est pas correcte", 'error');
    }

    if(getOneByColumn("match_types", "name", $name)){
        setFlashMessage("Le nom du match_types exite déjà", "error");
    }

    // Si pas d'erreurs de validation
    if (!hasErrors()) {
        $inserted = insert("match_types", [
            "name" => $name,
            "description" => $description,
            "path" => $path,
        ]);

        if ($inserted) {
            setFlashMessage("match_types enregistré: $name", "success");
            unset($name, $code_iso, $path);
            redirectToUrl("/admin/match_types");
        } else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-4">Ajouter un match_types</h2>
        <a href="/admin/match_types" class="btn btn-dark">
            Retour
        </a>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <?= displayFlash(); ?>

        <div class="mb-3">
            <label for="name" class="form-label">Nom match_types</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $name ?? '' ?>">
        </div>

         <div class="mb-3">
            <label for="name" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control"><?= $description ?? '' ?></textarea>
        </div>

        <!-- Fichier -->
        <div class="mb-3">
            <label for="path" class="form-label">Image Url</label>
            <input type="url" class="form-control" id="path" name="path" value="<?= $path ?? '' ?>">
        </div>

        <button type="submit" name="submit_form" class="btn btn-primary">Envoyer</button>
    </form>
</div>
<?php include_once __DIR__ . '/../include/footer.php'; ?>