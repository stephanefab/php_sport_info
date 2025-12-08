<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
$match_types = getAll("match_types", $page, 50);
$count = getCountAll("match_types");
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des types de matches (<?= $count; ?>)</h2>
        <a href="/admin/match_types/create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajouter un type de match
        </a>
    </div>
    <?= displayFlash(); ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>-</th>
                        <th>Description</th>
                        <th>Date ajout</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($match_types['data'] as $c): ?>
                        <tr id="<?= $c['id'] ?>">
                            <td><?= $c['description']; ?></td>
                            <td style="text-align:center;">
                                <figure style="margin:0; border:1px solid;">
                                    <img src="<?= $c['path'] ?>" alt="<?= $c['name']; ?>"
                                        style="width:40px; height:auto; display:block; margin:auto;">
                                </figure>
                            </td>
                            <td><?= mb_strtoupper($c['name']) ?></td>
                            <td><?= $c['created_at'] ?? '-' ?></td>
                            <td class="text-end">
                                <a href="/admin/match_types/edit.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/match_types/delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Supprimer ce sport ?');">
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

            <?php for ($i = 1; $i <= $match_types['totalPages']; $i++): ?>
                <li class="page-item <?= ($i == $match_types['currentPage']) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

        </ul>
    </nav>

</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>