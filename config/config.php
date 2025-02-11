<?php
define('BASE_URL', 'https://k1qrd5-tst-protheus.totvscloud.com.br:33389/api/WSDEMANDAS');
define('API_USER', 'candidato');
define('API_PASS', 'cape123');
define('APP_ENV', 'development'); 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
