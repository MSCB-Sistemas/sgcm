<?php
    define('APP', dirname(dirname(__FILE__)));
    
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cementerio');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'){
        $protocolo = 'https://';
    }
    else{
        $protocolo = 'http://';
    }
    
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = '/sgcm/';

    define('URL', $protocolo . $host . $baseUrl);
?>