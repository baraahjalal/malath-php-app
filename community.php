<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Controllers\CommunityController;
$ctrl = new CommunityController();
$_SERVER['REQUEST_METHOD'] === 'POST' ? $ctrl->handlePost() : $ctrl->index();
