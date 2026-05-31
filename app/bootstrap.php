<?php
define('ROOT_PATH', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/autoload.php';
require_once ROOT_PATH . '/includes/csrf.php';

csrf_generate();
