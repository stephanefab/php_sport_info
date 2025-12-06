<?php
require_once __DIR__ . '/../../functions.php';
isAdmin();
$user = getCurrentUser();

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$countries = getAll("pays", $page, 20);
?>

<?php include_once __DIR__ . '/../include/header.php'; ?>

<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des Pays</h2>
        <a href="/admin/countries/create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajouter un pays
        </a>
    </div>
    <?= displayFlash(); ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>-</th>
                        <th>Date ajout</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($countries['data'] as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['code_iso']) ?></td>
                            <td style="text-align:center;">
                                <figure style="margin:0;">
                                    <img src="<?= htmlspecialchars($c['path']) ?>" alt="<?= $c['code_iso']; ?>"
                                        style="width:40px; height:auto; display:block; margin:auto;">
                                </figure>
                            </td>

                            <td><?= $c['created_at'] ?? '-' ?></td>
                            <td class="text-end">
                                <a href="/admin/countries/edit.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/countries/delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Supprimer ce pays ?');">
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

            <?php for ($i = 1; $i <= $countries['totalPages']; $i++): ?>
                <li class="page-item <?= ($i == $countries['currentPage']) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

        </ul>
    </nav>

</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>