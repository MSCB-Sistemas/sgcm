<?php
if (!function_exists('renderIcon')) {
  function renderIcon(array $it): string {
      if (!empty($it['icon'])) {
          $href = htmlspecialchars($it['icon']);
          return '<svg class="bi pe-none me-2" width="16" height="16" aria-hidden="true"><use xlink:href="' . $href . '"></use></svg>';
      }
      if (!empty($it['bi'])) {
          $cls = 'bi bi-' . preg_replace('/[^a-z0-9\-]/i', '', $it['bi']) . ' me-2';
          return '<i class="' . $cls . '" aria-hidden="true"></i>';
      }
      return '';
  }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand d-flex align-items-center">
            <img src="<?= rtrim(URL,'/') . '/img/logo_claro.png' ?>" alt="Logo" class="me-2">
        </span>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($MENU as $item): ?>
                    <?php
                        $perms = $item['perms'] ?? [];
                        if (!empty($perms) && !$canAny($perms)) continue;

                        $hasChildren = !empty($item['children']);
                        $children = [];
                        if ($hasChildren) {
                            foreach ($item['children'] as $ch) {
                                $chPerms = $ch['perms'] ?? [];
                                if (empty($chPerms) || $canAny($chPerms)) $children[] = $ch;
                            }
                            if (empty($children)) $hasChildren = false;
                        }

                        $itemHref = $item['href'] ?? '#';
                        $itemActive = false;
                        if ($itemHref !== '#') {
                            $itemPath = trim(parse_url($itemHref, PHP_URL_PATH), '/');
                            if (str_starts_with($activePath, $itemPath)) $itemActive = true;
                        }
                    ?>

                    <?php if (!$hasChildren): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= $itemActive ? 'active' : '' ?>" href="<?= htmlspecialchars($itemHref) ?>">
                                <?= renderIcon($item) ?>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= renderIcon($item) ?>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                            <ul class="dropdown-menu shadow">
                                <?php foreach ($children as $ch): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= htmlspecialchars($ch['href']) ?>">
                                            <?= renderIcon($ch) ?>
                                            <?= htmlspecialchars($ch['label']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <div class="d-flex align-items-center">
                <a class="nav-link text-white me-3 d-none d-md-block" href="<?= URL . 'public/docs/Manual-de-usuario-sgcm.pdf' ?>" target="_blank" title="Manual de Usuario">
                    <i class="bi bi-book fs-5"></i>
                </a>
                
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle border-0 fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= ucfirst($_SESSION['usuario_nombre']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="px-3 py-2 border-bottom mb-2 text-muted small">
                            <?= ucfirst($_SESSION['usuario_nombre'])." ".ucfirst($_SESSION['usuario_apellido']); ?>
                        </li>
                        <li><button class="dropdown-item" id="toggleThemeNav"><i class="bi bi-moon-stars me-2"></i>Cambiar tema</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= URL ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

