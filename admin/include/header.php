<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (!empty($pageTitle)) ? htmlspecialchars($pageTitle) : 'Admin Panel' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-dark: #1a1d21;
            --secondary-dark: #2d3238;
            --accent-color: #3498db;
            --accent-hover: #2980b9;
        }

        body {
            background-color: #f4f6f9;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            padding: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand a {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
        }

        .sidebar-brand i {
            color: #f39c12;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 1rem;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 0.25rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--accent-color);
            color: #fff;
        }

        .sidebar-menu a i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            margin-top: 1rem;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top navbar */
        .top-navbar {
            background: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-navbar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Stats cards */
        .stat-card {
            border-radius: 12px;
            padding: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .stat-card .stat-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.3;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Forms */
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.6rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            border: 2px solid #e9ecef;
            border-right: none;
            background-color: #f8f9fa;
        }

        .input-group .form-control {
            border-left: none;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
        }

        /* Badge */
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 6px;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="/admin">
            <i class="bi bi-trophy-fill"></i> Admin Panel
        </a>
    </div>

    <div class="sidebar-section">Menu Principal</div>
    <ul class="sidebar-menu">
        <li>
            <a href="/admin" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['REQUEST_URI'], '/admin') !== false && !strpos($_SERVER['REQUEST_URI'], '/admin/') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/index.php">
                <i class="bi bi-house-door"></i> Voir le site
            </a>
        </li>
    </ul>

    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-menu">
        <li>
            <a href="/admin/countries" class="<?= strpos($_SERVER['REQUEST_URI'], '/countries') !== false ? 'active' : '' ?>">
                <i class="bi bi-globe-americas"></i> Pays
            </a>
        </li>
        <li>
            <a href="/admin/sports" class="<?= strpos($_SERVER['REQUEST_URI'], '/sports') !== false ? 'active' : '' ?>">
                <i class="bi bi-dribbble"></i> Sports
            </a>
        </li>
        <li>
            <a href="/admin/teams" class="<?= strpos($_SERVER['REQUEST_URI'], '/teams') !== false ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Equipes
            </a>
        </li>
        <li>
            <a href="/admin/match_types" class="<?= strpos($_SERVER['REQUEST_URI'], '/match_types') !== false ? 'active' : '' ?>">
                <i class="bi bi-collection"></i> Types de Match
            </a>
        </li>
        <li>
            <a href="/admin/matchs" class="<?= strpos($_SERVER['REQUEST_URI'], '/matchs') !== false ? 'active' : '' ?>">
                <i class="bi bi-calendar-event"></i> Matchs
            </a>
        </li>
    </ul>

    <div class="sidebar-section">Compte</div>
    <ul class="sidebar-menu">
        <li>
            <a href="/me">
                <i class="bi bi-person-circle"></i> Mon Profil
            </a>
        </li>
        <li>
            <a href="/logout.php" class="text-danger">
                <i class="bi bi-box-arrow-right"></i> Deconnexion
            </a>
        </li>
    </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-lg-none me-2" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-4"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none">Admin</a></li>
                    <?php if(isset($pageTitle)): ?>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3 text-muted d-none d-md-inline">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?>
            </span>
            <a href="/logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="p-4 fade-in">
