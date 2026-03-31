<?php require_once APP . '/views/inc/header.php' ?>

<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center py-4">
    <main class="container-fluid d-flex justify-content-center align-items-center px-3">
        <div class="w-100" style="max-width: 500px;">
            <?php require_once $viewPath; ?>
        </div>
    </main>

<?php require_once APP . '/views/inc/footer.php' ?>