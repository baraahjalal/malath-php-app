<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Controllers\AuthController;
$ctrl = new AuthController();
$_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->handleRegister() : $ctrl->showRegister();
