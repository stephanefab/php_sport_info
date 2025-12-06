<?php
    require_once __DIR__ . '/../functions.php';
    if(!isAdmin()){
        redirectToLogin();
    }
?>

<?php include_once __DIR__ . '/include/header.php'; ?>
<h1>Admin</h1>
<?php include_once __DIR__ . '/include/footer.php'; ?>
