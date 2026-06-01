<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Controllers\ProfileController;
$ctrl = new ProfileController();
$_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->handlePostAction() : $ctrl->index();
