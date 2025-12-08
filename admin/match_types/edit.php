<?php
    require_once __DIR__ . '/../../functions.php';
    isAdmin();
    $user = getCurrentUser();

    $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

    if(!$id){
        redirectToHome();
    }

    $is_match_type = getOneByColumn("match_types", "id", $id);
    if(!$is_match_type){
        setFlashMessage("ID #$id inconnu.", "error");
        redirectToUrl("/admin/match_types");
    }else{
        $name = $is_match_type['name'];
        $description = $is_match_type['description'];
        $path = $is_match_type['path'];
    }

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

    // Si pas d'erreurs de validation
    if (!hasErrors()) {

        $exist_name = getOneByColumn("match_types", "name", $name);
        if($exist_name && $exist_name['id'] != $id){
            setFlashMessage("Le nom '$name' est déjà utilisé", "error");
        }
        
        if (!hasErrors()){
             $updated = update("match_types", $id, [
                "name"     => $name,
                "description"     => $description,
                "path"     => $path,
            ]);

            if ($updated) {
                setFlashMessage("match_types modifié: $name", "success");
            } else {
                setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
            }
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>
<div class="container my-4">
     <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-4">Modifier le match_types : <?= $name ?></h2>
        <a href="/admin/match_types#<?= $id ?>" class="btn btn-dark">
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

        <button type="submit" name="submit_form" class="btn btn-primary">Modifier</button>
    </form>
</div>
<?php include_once __DIR__ . '/../include/footer.php'; ?>
