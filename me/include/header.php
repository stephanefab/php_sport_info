<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (!empty($pageTitle)) ? htmlspecialchars($pageTitle) : 'Sports Comments' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/index.php">UserPanel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser" aria-controls="navbarUser" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarUser">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="/index.php">Home</a>
        </li>
        <?php if(getCurrentUser()['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="/admin">Dashboard</a>
            </li>
            <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link" href="/me">Profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main class="container my-4">
