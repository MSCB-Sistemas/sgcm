<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.use_strict_mode', 1);
  session_start();
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/errores.php';
require_once __DIR__ . '/helpers/authHelper.php';
require_once __DIR__ . '/lib/Control.php';

spl_autoload_register(function ($class) {
  $paths = [
    __DIR__ . '/lib/'         . $class . '.php',
    __DIR__ . '/models/'      . $class . '.php',
    __DIR__ . '/controllers/' . $class . '.php',
  ];
  foreach ($paths as $file) {
    if (file_exists($file)) {
      require_once $file;
      return;
    }
  }
});
