<?php
require_once __DIR__ . '/../functions.php';
isAdmin();
$user = getCurrentUser();
?>

<?php include_once __DIR__ . '/include/header.php'; ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4">Dashboard Admin</h1>
            <p class="lead">Bienvenue, <strong><?= htmlspecialchars($user['name']) ?></strong> !</p>
        </div>
    </div>

    <!-- Gestion -->
    <div class="row g-4 mb-5">
        <?php 
        $sections = [
            ['title' => 'Pays', 'color' => 'primary', 'text' => 'Gérer la liste des pays', 'link' => '/admin/countries', 'count' => getCountAll('pays')],
            ['title' => 'Sports', 'color' => 'success', 'text' => 'Gérer les sports', 'link' => '/admin/sports', 'count' => getCountAll('sports')],
            ['title' => 'Equipes', 'color' => 'warning', 'text' => 'Gérer les équipes', 'link' => '/admin/teams', 'count' => getCountAll('teams')],
            ['title' => 'Matchs', 'color' => 'danger', 'text' => 'Gérer les matchs', 'link' => '/admin/matchs', 'count' => getCountAll('matches')],
        ];

        foreach($sections as $sec): ?>
        <div class="col-lg-3 col-md-6">
            <div class="card text-white bg-<?= $sec['color'] ?> h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title"><?= $sec['title'] ?></h5>
                        <p class="card-text"><?= $sec['text'] ?></p>
                    </div>
                    <div class="mt-3">
                        <p class="h3"><?= $sec['count'] ?></p>
                        <a href="<?= $sec['link'] ?>" class="btn btn-light btn-sm">Voir</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include_once __DIR__ . '/include/footer.php'; ?>
