<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ArticleModel;
use App\Models\CommunityModel;

class ArticleController extends Controller {

    public function index(): void {
        $model       = new ArticleModel();
        $commModel   = new CommunityModel();

        $currentSlug = $_GET['c'] ?? null;
        $page        = max(1, intval($_GET['page'] ?? 1));
        $perPage     = 9;
        $offset      = ($page - 1) * $perPage;

        $articles    = $model->getApproved($currentSlug, $perPage, $offset);
        $total       = $model->countApproved($currentSlug);
        $totalPages  = (int)ceil($total / $perPage);
        $communities = $commModel->getAll();

        $this->view('articles.index', compact(
            'articles', 'page', 'totalPages', 'total', 'currentSlug', 'communities'
        ));
    }

    public function create(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /malath-php-app/login');
            exit;
        }
        $communities = (new CommunityModel())->getAll();
        $sent        = isset($_GET['sent']);
        $this->view('articles.create', compact('communities', 'sent'));
    }

    public function handleCreate(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /malath-php-app/login.php');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403); exit('طلب غير صالح');
        }

        $title       = trim($_POST['title']   ?? '');
        $content     = trim($_POST['content'] ?? '');
        $communityId = intval($_POST['community_id'] ?? 0);
        $userId      = (int)$_SESSION['user_id'];

        $errors = [];
        if (mb_strlen($title)   < 5)  $errors[] = 'العنوان قصير جداً (5 أحرف على الأقل)';
        if (mb_strlen($content) < 50) $errors[] = 'المحتوى قصير جداً (50 حرفاً على الأقل)';
        if (!$communityId)            $errors[] = 'اختاري التصنيف المناسب';

        if ($errors) {
            $communities = (new CommunityModel())->getAll();
            $this->view('articles.create', compact('communities', 'errors', 'title', 'content', 'communityId'));
            return;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true)) {
                $dir = ROOT_PATH . '/assets/uploads/articles/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'article_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename)) {
                    $imagePath = 'assets/uploads/articles/' . $filename;
                }
            }
        }

        $model = new ArticleModel();
        $model->createSuggestion($userId, $communityId, $title, $content, $imagePath);

        header('Location: /malath-php-app/article-create.php?sent=1');
        exit;
    }

    public function show(): void {
        $id    = intval($_GET['id'] ?? 0);
        $model = new ArticleModel();

        if (!$id || !($article = $model->getById($id))) {
            http_response_code(404);
            include ROOT_PATH . '/includes/header.php';
            echo '<div style="text-align:center;padding:6rem 0;">
                    <i class="fa-solid fa-file-circle-xmark" style="font-size:3rem;color:var(--outline-variant);display:block;margin-bottom:1rem;"></i>
                    <h2 style="color:var(--secondary);">المقال غير موجود أو غير منشور بعد.</h2>
                    <a href="/malath-php-app/articles.php" class="btn-outline" style="margin-top:1.5rem;display:inline-block;text-decoration:none;">← جميع المقالات</a>
                  </div>';
            include ROOT_PATH . '/includes/footer.php';
            return;
        }

        $userId  = $_SESSION['user_id'] ?? null;
        $isSaved = $userId ? $model->isSaved((int)$userId, $id) : false;

        $this->view('articles.single', compact('article', 'isSaved'));
    }
}
