<?php
require_once __DIR__ . '/app/bootstrap.php';
use App\Controllers\ArticleController;
$ctrl = new ArticleController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl->handleCreate();
} else {
    $ctrl->create();
}
