<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Ajouter un pays";

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

    if (getOneByColumn("pays", "name", $name)) {
        setFlashMessage("Ce nom de pays existe deja", "error");
    }

    if (!hasErrors()) {
        $inserted = insert("pays", [
            "name"     => $name,
            "code_iso" => strtoupper($code_iso),
            "path"     => $path,
        ]);

        if ($inserted) {
            // Créer automatiquement l'équipe nationale
            insert("teams", [
                "name"       => $name,
                "type"       => 'national',
                "country_id" => $inserted,
                "path"       => $path,
            ]);
            setFlashMessage("Pays ajoute avec succes : $name", "success");
            redirectToUrl("/admin/countries");
        } else {
            setFlashMessage("Une erreur est survenue", 'error');
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Ajouter un pays</h1>
        <p class="text-muted mb-0">Creer un nouveau pays dans la base de donnees</p>
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
                                       value="<?= htmlspecialchars($name ?? '') ?>" required
                                       placeholder="Ex: France" minlength="2" maxlength="100">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="code_iso" class="form-label">
                                Code ISO <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control" id="code_iso" name="code_iso"
                                       value="<?= htmlspecialchars($code_iso ?? '') ?>" required
                                       placeholder="Ex: FR" minlength="2" maxlength="10" style="text-transform: uppercase;">
                            </div>
                            <div class="form-text">Code pays ISO 3166-1 (2-3 lettres)</div>
                        </div>

                        <div class="col-12">
                            <label for="path" class="form-label">URL du drapeau</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="url" class="form-control" id="path" name="path"
                                       value="<?= htmlspecialchars($path ?? '') ?>"
                                       placeholder="https://exemple.com/drapeau.png">
                            </div>
                            <div class="form-text">Optionnel - URL d'une image du drapeau</div>
                        </div>

                        <!-- Preview -->
                        <div class="col-12" id="preview-container" style="display: none;">
                            <label class="form-label">Apercu</label>
                            <div>
                                <img id="flag-preview" src="" alt="Apercu" class="rounded border"
                                     style="max-height: 60px;">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" name="submit_form" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="/admin/countries" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-info-circle me-1"></i> Information
                </h6>
                <p class="card-text small text-muted">
                    Lorsque vous ajoutez un pays, une equipe nationale est automatiquement creee avec le meme nom et le meme drapeau.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('path').addEventListener('input', function(e) {
    const url = e.target.value;
    const preview = document.getElementById('flag-preview');
    const container = document.getElementById('preview-container');

    if (url && url.match(/^https?:\/\/.+/)) {
        preview.src = url;
        preview.onload = () => container.style.display = 'block';
        preview.onerror = () => container.style.display = 'none';
    } else {
        container.style.display = 'none';
    }
});
</script>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
