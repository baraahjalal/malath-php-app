<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Controllers\AuthController;
(new AuthController())->logout();
