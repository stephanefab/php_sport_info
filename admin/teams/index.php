<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$teams = getAll("teams", $page, 50);
$count = getCountAll("teams");
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des équipes (<?= $count; ?>)</h2>
        <a href="/admin/teams/create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajouter une équipe
        </a>
    </div>
    <?= displayFlash(); ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>type</th>
                        <th>-</th>
                        <th>Date ajout</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($teams['data'] as $c): ?>
                        <tr id="<?= $c['id'] ?>">
                            <td><?= mb_strtoupper($c['name']) ?></td>
                            <td><?= mb_strtoupper($c['type']) ?></td>
                            <td style="text-align:center;">
                                <figure style="margin:0; border:1px solid;">
                                    <img src="<?= $c['path'] ?>" alt="<?= $c['name']; ?>"
                                        style="width:40px; height:auto; display:block; margin:auto;">
                                </figure>
                            </td>

                            <td><?= $c['created_at'] ?? '-' ?></td>
                            <td class="text-end">
                                <a href="/admin/teams/edit.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/teams/delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Supprimer cette équipe ?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <nav class="mt-4">
        <ul class="pagination justify-content-center">

            <?php for ($i = 1; $i <= $teams['totalPages']; $i++): ?>
                <li class="page-item <?= ($i == $teams['currentPage']) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

        </ul>
    </nav>

</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>