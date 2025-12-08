<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

$match_types = query("select * from match_types")->fetchAll();
$sports = query("select * from sports")->fetchAll();
$matchs = query("select * from matchs")->fetchAll();
$teams = query("select * from teams")->fetchAll();

if (isset($_POST['submit_form'])) {
    $match_type_id = (int) sanitizeInput($_POST['match_type_id'] ?? '0');
    $sport_id = (int) sanitizeInput($_POST['sport_id'] ?? '0');
    $team_home_id = (int) sanitizeInput($_POST['team_home_id'] ?? '0');
    $team_away_id = (int) sanitizeInput($_POST['team_away_id'] ?? '0');
    $score_home = (int) sanitizeInput($_POST['score_home'] ?? '0');
    $score_away = (int) sanitizeInput($_POST['score_away'] ?? '0');
    $match_date = $_POST['match_date'] ?? '';
    $match_hour = $_POST['match_hour'] ?? '';
    $status = sanitizeInput($_POST['status'] ?? '');

    if (!$match_type_id || !$sport_id || !$team_home_id || !$team_away_id || !$status) {
        setFlashMessage("Tous les champs sont requis", 'error');
    }

    if ($team_home_id === $team_away_id) {
        setFlashMessage("Les equipes sont pareil", 'error');
    }

    if ((!empty($score_home) && $score_home < 0) || !empty($score_away) && $score_away < 0) {
        setFlashMessage("Le score ne doit pas être négatif", 'error');
    }

    if (!in_array($status, ['planned', 'playing', 'finished'])) {
        setFlashMessage("status '$status' inconnu", 'error');
    }

    if (!getOneByColumn("match_types", "id", $match_type_id)) {
        setFlashMessage("ID du match_type inconnu", 'error');
    }
    if (!getOneByColumn("sports", "id", $sport_id)) {
        setFlashMessage("ID du sport inconnu", 'error');
    }
    if (!getOneByColumn("teams", "id", $team_home_id)) {
        setFlashMessage("ID du team home inconnu", 'error');
    }
    if (!getOneByColumn("teams", "id", $team_away_id)) {
        setFlashMessage("ID du team away inconnu", 'error');
    }

    // Si pas d'erreurs de validation
    if (!hasErrors()) {
        $inserted = insert("matchs", [
            "match_type_id" => $match_type_id,
            "sport_id"      => $sport_id,
            "team_home_id"  => $team_home_id,
            "team_away_id"  => $team_away_id,
            "score_home"    => $score_home,
            "score_away"    => $score_away,
            "match_date"    => $match_date,
            "match_hour"    => $match_hour,
            "status"        => $status
        ]);

        if ($inserted) {
            setFlashMessage("match enregistrée", "success");
            unset($name, $code_iso, $path);
            redirectToUrl("/admin/matchs");
        } else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
    }
}
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-4">Ajouter une match</h2>
        <a href="/admin/matchs" class="btn btn-dark">
            Retour
        </a>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <?= displayFlash(); ?>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control" id="status">
                <option class="form-control" value="">--</option>
                <option class="form-control" <?= (isset($status) && $status === "planned") ? "selected" : '' ?>
                    value="planned">Prévu</option>
                <option class="form-control" <?= (isset($status) && $status === "playing") ? "selected" : '' ?>
                    value="playing">En cours
                </option>
                <option class="form-control" <?= (isset($status) && $status === "finished") ? "selected" : '' ?>
                    value="finished">Terminé
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="match_type_id" class="form-label">Match type</label>
            <select name="match_type_id" class="form-control" id="match_type_id">
                <option class="form-control" value="">--</option>
                <?php if (!empty($match_types)): ?>
                    <?php foreach ($match_types as $match_type): ?>
                        <option class="form-control" <?= (isset($match_type_id) && $match_type_id === $match_type['id']) ? "selected" : '' ?> value="<?= $match_type['id'] ?>"><?= mb_strtolower($match_type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="sport_id" class="form-label">Sport</label>
            <select name="sport_id" class="form-control" id="sport_id">
                <option class="form-control" value="">--</option>
                <?php if (!empty($sports)): ?>
                    <?php foreach ($sports as $sport): ?>
                        <option class="form-control" <?= (isset($sport_id) && $sport_id === $sport['id']) ? "selected" : '' ?>
                            value="<?= $sport['id'] ?>"><?= mb_strtolower($sport['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <hr>
        <div class="mb-3">
            <label for="team_home_id" class="form-label">Equipe domicile</label>
            <select name="team_home_id" class="form-control" id="team_home_id">
                <option class="form-control" value="">--</option>
                <?php if (!empty($teams)): ?>
                    <?php foreach ($teams as $team_home): ?>
                        <option class="form-control" <?= (isset($team_home_id) && $team_home_id === $team_home['id']) ? "selected" : '' ?> value="<?= $team_home['id'] ?>"><?= mb_strtolower($team_home['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="team_away_id" class="form-label">Equipe invité</label>
            <select name="team_away_id" class="form-control" id="team_away_id">
                <option class="form-control" value="">--</option>
                <?php if (!empty($teams)): ?>
                    <?php foreach ($teams as $team_away): ?>
                        <option class="form-control" <?= (isset($team_away_id) && $team_away_id === $team_away['id']) ? "selected" : '' ?> value="<?= $team_away['id'] ?>"><?= mb_strtolower($team_away['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <hr>

        <div class="mb-3">
            <label for="score_home" class="form-label">Score domicile</label>
            <input type="text" class="form-control" id="score_home" name="score_home" value="<?= $score_home ?? '' ?>">
        </div>


        <div class="mb-3">
            <label for="score_away" class="form-label">Score invité</label>
            <input type="text" class="form-control" id="score_away" name="score_away" value="<?= $score_away ?? '' ?>">
        </div>
        <!-- Fichier -->
        <div class="mb-3">
            <label for="match_date" class="form-label">Date du match</label>
            <input type="date" class="form-control" id="match_date" name="match_date" value="<?= $match_date ?? '' ?>">
        </div>

        <div class="mb-3">
            <label for="match_hour" class="form-label">Heure du match</label>
            <input type="time" class="form-control" id="match_hour" name="match_hour" value="<?= $match_hour ?? '' ?>">
        </div>

        <button type="submit" name="submit_form" class="btn btn-primary">Envoyer</button>
    </form>
</div>
<?php include_once __DIR__ . '/../include/footer.php'; ?>