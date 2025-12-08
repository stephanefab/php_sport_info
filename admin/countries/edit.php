<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Modifier un pays";

$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

if (!$id) {
    redirectToUrl("/admin/countries");
}

$country = getOneByColumn("pays", "id", $id);
if (!$country) {
    setFlashMessage("Pays introuvable", "error");
    redirectToUrl("/admin/countries");
}

$name = $country['name'];
$code_iso = $country['code_iso'];
$path = $country['path'];

if (isset($_POST['submit_form'])) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $code_iso = sanitizeInput($_POST['code_iso'] ?? '', true);
    $path = sanitizeInput($_POST['path'] ?? '');

    if (!$name || !$code_iso) {
        setFlashMessage("Le nom et le code ISO sont requis", 'error');
    }

    if (strlen($name) < 2 || strlen($name) > 100) {
        setFlashMessage("Le nom doit contenir entre 2 et 100 caracteres", 'error');
    }

    if (strlen($code_iso) < 2 || strlen($code_iso) > 10) {
        setFlashMessage("Le code ISO doit contenir entre 2 et 10 caracteres", 'error');
    }

    if (!empty($path) && !filter_var($path, FILTER_VALIDATE_URL)) {
        setFlashMessage("L'URL de l'image n'est pas valide", 'error');
    }

    if (!hasErrors()) {
        $existName = getOneByColumn("pays", "name", $name);
        if ($existName && $existName['id'] != $id) {
            setFlashMessage("Ce nom de pays est deja utilise", "error");
        }

        $existIso = getOneByColumn("pays", "code_iso", strtoupper($code_iso));
        if ($existIso && $existIso['id'] != $id) {
            setFlashMessage("Ce code ISO est deja utilise", "error");
        }

        if (!hasErrors()) {
            $updated = update("pays", $id, [
                "name"     => $name,
                "code_iso" => strtoupper($code_iso),
                "path"     => $path,
            ]);

            if ($updated) {
                setFlashMessage("Pays modifie avec succes", "success");
            } else {
                setFlashMessage("Une erreur est survenue", 'error');
            }
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Modifier : <?= htmlspecialchars($country['name']) ?></h1>
        <p class="text-muted mb-0">ID #<?= $id ?></p>
    </div>
    <a href="/admin/countries" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<?php displayFlash(); ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Nom du pays <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?= htmlspecialchars($name) ?>" required
                                       minlength="2" maxlength="100">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="code_iso" class="form-label">
                                Code ISO <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control" id="code_iso" name="code_iso"
                                       value="<?= htmlspecialchars($code_iso) ?>" required
                                       minlength="2" maxlength="10" style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="path" class="form-label">URL du drapeau</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="url" class="form-control" id="path" name="path"
                                       value="<?= htmlspecialchars($path) ?>">
                            </div>
                        </div>

                        <?php if ($path): ?>
                        <div class="col-12">
                            <label class="form-label">Drapeau actuel</label>
                            <div>
                                <img src="<?= htmlspecialchars($path) ?>" alt="<?= htmlspecialchars($name) ?>"
                                     class="rounded border" style="max-height: 60px;">
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" name="submit_form" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Modifier
                        </button>
                        <a href="/admin/countries" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle me-1"></i> Zone de danger
            </div>
            <div class="card-body">
                <p class="card-text small text-muted">
                    La suppression d'un pays est irreversible et peut affecter les equipes associees.
                </p>
                <a href="/admin/countries/delete.php?id=<?= $id ?>" class="btn btn-outline-danger btn-sm"
                   onclick="return confirm('Supprimer definitivement ce pays ?');">
                    <i class="bi bi-trash me-1"></i> Supprimer ce pays
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
