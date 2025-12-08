<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$pageTitle = "Equipes";

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;

// Requête avec jointure pour avoir le nom du pays
$offset = ($page - 1) * 20;
$teams = query("
    SELECT t.*, p.name AS country_name, p.path AS country_path
    FROM teams t
    LEFT JOIN pays p ON t.country_id = p.id
    ORDER BY t.id DESC
    LIMIT 20 OFFSET $offset
")->fetchAll();

$count = getCountAll("teams");
$totalPages = ceil($count / 20);
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Gestion des Equipes</h1>
        <p class="text-muted mb-0"><?= $count ?> equipes enregistrees</p>
    </div>
    <a href="/admin/teams/create.php" class="btn btn-warning">
        <i class="bi bi-plus-circle me-1"></i> Ajouter une equipe
    </a>
</div>

<?php displayFlash(); ?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($teams)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people-fill fs-1 mb-3 d-block"></i>
                <p class="mb-3">Aucune equipe enregistree</p>
                <a href="/admin/teams/create.php" class="btn btn-warning btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajouter la premiere equipe
                </a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Nom</th>
                        <th>Pays</th>
                        <th>Type</th>
                        <th>Date d'ajout</th>
                        <th class="text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $t): ?>
                    <tr id="team-<?= $t['id'] ?>">
                        <td>
                            <?php if ($t['path']): ?>
                                <img src="<?= htmlspecialchars($t['path']) ?>" alt="<?= htmlspecialchars($t['name']) ?>"
                                     class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-warning bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-shield text-warning"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-semibold"><?= htmlspecialchars($t['name']) ?></span>
                        </td>
                        <td>
                            <?php if ($t['country_name']): ?>
                                <div class="d-flex align-items-center">
                                    <?php if ($t['country_path']): ?>
                                        <img src="<?= htmlspecialchars($t['country_path']) ?>" alt=""
                                             class="me-2" style="width: 20px; height: 15px; object-fit: cover;">
                                    <?php endif; ?>
                                    <small><?= htmlspecialchars($t['country_name']) ?></small>
                                </div>
                            <?php else: ?>
                                <small class="text-muted">-</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $t['type'] === 'national' ? 'primary' : 'secondary' ?>">
                                <?= $t['type'] === 'national' ? 'National' : 'Club' ?>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="/admin/teams/edit.php?id=<?= $t['id'] ?>"
                                   class="btn btn-outline-warning" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/teams/delete.php?id=<?= $t['id'] ?>"
                                   class="btn btn-outline-danger" title="Supprimer"
                                   onclick="return confirm('Supprimer l\'equipe <?= htmlspecialchars(addslashes($t['name'])) ?> ?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
