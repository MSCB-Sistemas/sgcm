<?php require_once APP . '/views/inc/header.php' ?>
<link rel="stylesheet" href="<?= URL ?>/public/css/home.css">
<?php require_once APP . '/views/inc/navbar.php' ?>

<main class="container">
    <div class="welcome-container">
        <h1 class="welcome-text">Bienvenido,
            <?= htmlspecialchars($datos['usuario']['nombre'] . ' ' . $datos['usuario']['apellido']) ?></h1>
            <div class="decorative-line"></div>
            <p class="sub-welcome">
                Sistema de Gestión de Cementerio Municipal.<br>
                Use la navegación superior para acceder a las diferentes secciones del sistema.
            </p>
    </div>
</main>

<?php require_once APP . '/views/inc/footer.php' ?>