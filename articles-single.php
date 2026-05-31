<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Controllers\ArticleController;

$ctrl = new ArticleController();
$ctrl->show();
