<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PostModel;

class ArticleController extends Controller {

    public function index(): void {
        $model     = new PostModel();
        $page      = max(1, intval($_GET['page'] ?? 1));
        $perPage   = 9;
        $offset    = ($page - 1) * $perPage;
        $articles  = $model->getArticles($perPage, $offset);
        $total     = $model->countArticles();
        $totalPages = (int)ceil($total / $perPage);
        $this->view('articles.index', compact('articles', 'page', 'totalPages', 'total'));
    }

    public function show(): void {
        $id    = intval($_GET['id'] ?? 0);
        $model = new PostModel();

        if (!$id || !($article = $model->getArticleById($id))) {
            http_response_code(404);
            echo '<h2>المقال غير موجود.</h2>';
            return;
        }

        $comments = $model->getComments($id);
        $related  = $model->getArticles(3);

        $this->view('articles.single', compact('article', 'comments', 'related'));
    }
}
