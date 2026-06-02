<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\EmailService;
use App\Models\AdminModel;
use App\Models\ArticleModel;

class AdminController extends Controller {

    public function index(): void {
        $this->requireAdmin();
        $tab   = $_GET['tab'] ?? 'overview';
        $model = new AdminModel();
        $stats = $model->getStats();

        $recent_posts     = [];
        $recent_users     = [];
        $users            = [];
        $posts            = [];
        $pending_articles = [];
        $all_articles     = [];
        $articleModel     = new ArticleModel();
        $pending_count    = $articleModel->countPending();

        if ($tab === 'overview') {
            $recent_posts = $model->getRecentPosts(8);
            $recent_users = $model->getRecentUsers(6);
        } elseif ($tab === 'users') {
            $search = trim($_GET['search'] ?? '');
            $users  = $model->getUsers($search);
        } elseif ($tab === 'posts') {
            $posts = $model->getPosts();
        } elseif ($tab === 'articles') {
            $all_articles = $model->getAllArticles();
        }

        extract($stats);
        $this->view('admin.index', compact(
            'tab','total_users','total_posts','total_comments','total_likes',
            'new_users_week','new_posts_week','recent_posts','recent_users','users','posts',
            'pending_articles','pending_count','all_articles'
        ));
    }

    public function handlePost(): void {
        $this->requireAdmin();
        \csrf_verify();
        $model = new AdminModel();

        if (isset($_POST['delete_post'])) {
            $model->deletePost((int)$_POST['post_id']);
            $this->redirect('/malath-php-app/dashboard.php?tab=posts&msg=post_deleted');
        }

        if (isset($_POST['delete_user'])) {
            $uid = (int)$_POST['target_user_id'];
            if ($uid !== (int)$_SESSION['user_id']) {
                $model->deleteUser($uid);
            }
            $this->redirect('/malath-php-app/dashboard.php?tab=users&msg=user_deleted');
        }

        if (isset($_POST['toggle_role'])) {
            $uid  = (int)$_POST['target_user_id'];
            $role = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
            $model->setUserRole($uid, $role);
            $this->redirect('/malath-php-app/dashboard.php?tab=users');
        }

        if (isset($_POST['approve_article'])) {
            $articleModel = new ArticleModel();
            $articleId    = (int)$_POST['article_id'];
            $article      = $articleModel->getArticleWithAuthor($articleId);
            $articleModel->approve($articleId);
            if ($article) {
                EmailService::sendArticleStatus(
                    $article['author_email'],
                    $article['author_name'],
                    $article['title'],
                    'approved'
                );
            }
            $this->redirect('/malath-php-app/dashboard.php?tab=articles&msg=article_approved');
        }

        if (isset($_POST['reject_article'])) {
            $articleModel = new ArticleModel();
            $articleId    = (int)$_POST['article_id'];
            $article      = $articleModel->getArticleWithAuthor($articleId);
            $articleModel->reject($articleId);
            if ($article) {
                EmailService::sendArticleStatus(
                    $article['author_email'],
                    $article['author_name'],
                    $article['title'],
                    'rejected'
                );
            }
            $this->redirect('/malath-php-app/dashboard.php?tab=articles&msg=article_rejected');
        }

        $this->redirect('/malath-php-app/dashboard.php');
    }
}
