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


<h1 class="visually-hidden">Barra inicio</h1>
<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark " style="width: 289px; background-color: #fd7e14 !important;">
    <a class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none" href="https://www.bariloche.gov.ar/" target="_blank">
        <img src="<?= rtrim(URL,'/') . '/img/logo_claro.png' ?>" alt="Logo" width="240" height="80" class="pe-none me-2">
    </a>
    <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <?php foreach ($MENU as $item): ?>
                <?php
                    if (isset($item['perms'])) {
                        $perms = $item['perms'];
                    } else {
                        $perms = [];
                    }
                    $visible = empty($perms) || $canAny($perms);
                    if (!$visible) continue;


                    if (!empty($item['children'])) {
                        $hasChildren = true;
                    } else {
                        $hasChildren = false;
                    }
                    $children = [];
                    if ($hasChildren) {
                      foreach ($item['children'] as $ch) {
                          if (isset($ch['perms'])) {
                              $chPerms = $ch['perms'];
                          } else {
                              $chPerms = [];
                          }

                          if (empty($chPerms) || $canAny($chPerms)) $children[] = $ch;
                      }
                      if (empty($children)) $hasChildren = false;
                    }

                    if (isset($item['href'])) {
                        $itemHref = $item['href'];
                    } else {
                        $itemHref = null;
                    }

                    $itemActive = false;
                    if ($itemHref) {
                        $itemPath = trim(parse_url($itemHref, PHP_URL_PATH), '/');
                        if (str_starts_with($activePath, $itemPath)) $itemActive = true;
                    }
                ?>

                <?php if (!$hasChildren): ?>
                  <li class="nav-item">
                    <a class="nav-link text-white <?= $itemActive ? 'active' : '' ?>"
                        <?php
                            if (isset($itemHref)) {
                                echo 'href="' . htmlspecialchars($itemHref) . '"';
                            } else {
                                echo 'href="#"';
                            }
                        ?>>
                        <?= renderIcon($item) ?>
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                  </li>
                <?php else: ?>
                  <?php $grpId = 'grp-' . md5($item['label']); ?>
                  <li>
                    <a class="nav-link text-white position-relative"
                      data-bs-toggle="collapse"
                      data-bs-target="#<?= $grpId ?>"
                      href="#<?= $grpId ?>"
                      role="button" aria-expanded="false"
                      aria-controls="<?= $grpId ?>">
                      <?= renderIcon($item) ?>
                      <?= htmlspecialchars($item['label']) ?>
                      <span class="position-absolute end-0 me-3" id="<?= $grpId ?>-arrow">&#x25BC;</span>
                    </a>

                    <div class="collapse ps-3" id="<?= $grpId ?>">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <?php foreach ($children as $ch): ?>
                          <li>
                            <a class="nav-link text-white" href="<?= htmlspecialchars($ch['href']) ?>">
                                <?= renderIcon($ch) ?>                              
                                <?= htmlspecialchars($ch['label']) ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    
    <div class="dropup mt-auto text-center">
      <a class="nav-link text-white" href="<?= URL ?>manual_usuario.pdf" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-book me-2" viewBox="0 0 16 16">
                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
            </svg>
            Manual de Usuario
        </a>
        <hr>
        <a class="d-block text-white dropdown-toggle fw-bold fs-6 text-decoration-none" 
        href="#" 
        role="button" 
        data-bs-toggle="dropdown" 
        aria-expanded="false">
            <?= ucfirst($_SESSION['usuario_nombre'])." ".ucfirst($_SESSION['usuario_apellido']); ?>
        </a>
        <ul class="dropdown-menu text-small shadow">
            <li><button class="dropdown-item" id="toggleTheme">Cambiar tema</button></li>
            <li><a class="dropdown-item text-danger" href="<?= URL ?>logout">Cerrar sesión</a></li>
        </ul>
    </div>
</div>