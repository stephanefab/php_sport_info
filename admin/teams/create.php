<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

$countries = query("select * from pays")->fetchAll();

if (isset($_POST['submit_form'])) {
    $name = sanitizeInput($_POST['name'] ?? '', 1);
    $country_id = (int) sanitizeInput($_POST['country_id'] ?? '0');
    $type = sanitizeInput($_POST['type'] ?? '', 1);
    $path = sanitizeInput($_POST['path'] ?? '');

    if (!$name || !$type || !$country_id) {
        setFlashMessage("Tous les champs sont requis", 'error');
    }

    if (strlen($name) < 3 || strlen($name) > 100) {
        setFlashMessage("Le nom doit être compris entre 3 et 100 caractères", 'error');
    }

    if(!in_array($type, ['national', 'club'])){
        setFlashMessage("Type '$type' inconnu", 'error');
    }

    if (!getOneByColumn("pays", "id", $country_id)) {
        setFlashMessage("ID du pays inconnu", 'error');
    }

    if (!empty($path) && !filter_var($path, FILTER_VALIDATE_URL)) {
        setFlashMessage("L'url de l'image n'est pas correcte", 'error');
    }

    if(getOneByColumn("teams", "name", $name)){
        setFlashMessage("Le nom de l'équipe exite déjà", "error");
    }

    // Si pas d'erreurs de validation
    if (!hasErrors()) {
        $inserted = insert("teams", [
            "name"     => $name,
            "type" => $type,
            "country_id" => $country_id,
            "path"     => $path,
        ]);

        if ($inserted) {
            setFlashMessage("Equipe enregistrée: $name", "success");
            unset($name, $code_iso, $path);
            redirectToUrl("/admin/teams");
        } else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>
<div class="container my-4">
         <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-4">Ajouter une équipe</h2>
        <a href="/admin/teams" class="btn btn-dark">
            Retour
        </a>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <?= displayFlash(); ?>

        <div class="mb-3">
            <label for="name" class="form-label">Nom Equipe</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $name ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" class="form-control" id="type">
                <option class="form-control" value="">--</option>
                <option class="form-control" <?= (isset($type) && $type==="national") ? "selected" : '' ?> value="national">National</option>
                <option class="form-control" <?= (isset($type) && $type==="club") ? "selected" : '' ?> value="club">Club</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="country_id" class="form-label">Pays d'origine</label>
            <select name="country_id" class="form-control" id="country_id">
                <option class="form-control" value="">--</option>
                <?php if(!empty($countries)): ?>
                    <?php foreach($countries as $country):?>
                         <option class="form-control" <?= (isset($country_id) && $country_id===$country['id']) ? "selected" : '' ?> value="<?= $country['id'] ?>"><?= mb_strtolower($country['name'] )?></option>
                    <?php endforeach;?>
                <?php endif; ?>
            </select>
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