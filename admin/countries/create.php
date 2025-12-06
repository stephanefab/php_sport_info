<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

if (isset($_POST['submit_form'])) {
    $name = sanitizeInput($_POST['name'] ?? '', 1);
    $code_iso = sanitizeInput($_POST['code_iso'] ?? '', 1);
    $path = sanitizeInput($_POST['path'] ?? '');

    if (!$name || !$code_iso) {
        setFlashMessage("Tous les champs sont requis", 'error');
    }

    if (strlen($name) < 3 || strlen($name) > 100) {
        setFlashMessage("Le nom doit être compris entre 3 et 100 caractères", 'error');
    }

    if (strlen($code_iso) < 2 || strlen($code_iso) > 10) {
        setFlashMessage("Le code iso doit être compris entre 2 et 10 caractères", 'error');
    }

    if (!empty($path) && !filter_var($path, FILTER_VALIDATE_URL)) {
        setFlashMessage("L'url de l'image n'est pas correcte", 'error');
    }

    if(getOneByColumn("pays", "name", $name)){
        setFlashMessage("Le nom du pays exite déjà", "error");
    }

    // Si pas d'erreurs de validation
    if (!hasErrors()) {
        $inserted = insert("pays", [
            "name"     => $name,
            "code_iso" => $code_iso,
            "path"     => $path,
        ]);

        if ($inserted) {
            setFlashMessage("Pays enregistré: $name", "success");
            $inserted = insert("teams", [
                "name"       => $name,
                "type"       => 'national',
                "country_id" => $inserted,
                "path"       => $path,
            ]);
            unset($name, $code_iso, $path);
            redirectToUrl("/admin/countries");
        } else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>
<div class="container my-4">
         <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-4">Ajouter un pays</h2>
        <a href="/admin/countries" class="btn btn-dark">
            Retour
        </a>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <?= displayFlash(); ?>

        <div class="mb-3">
            <label for="name" class="form-label">Nom Pays</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $name ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="code_iso" class="form-label">Code iso</label>
            <input type="text" class="form-control" id="code_iso" name="code_iso" value="<?= $code_iso ?? '' ?>">
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