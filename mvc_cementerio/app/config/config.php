<?php
    define('APP', dirname(dirname(__FILE__)));
    
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cementerio');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = '/cementerio/mvc_cementerio/';

    define('URL', $protocolo . $host . $baseUrl);
?>