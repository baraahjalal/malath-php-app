<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PostModel;
use App\Models\CommunityModel;

class CommunityController extends Controller {

    public function index(): void {
        $user_id      = $_SESSION['user_id'] ?? null;
        $current_slug = $_GET['c'] ?? 'all';

        $commModel  = new CommunityModel();
        $postModel  = new PostModel();
        $communities     = $commModel->getAll();
        $user_communities = $user_id ? $commModel->getUserCommunities($user_id) : [];

        $current_community = null;
        foreach ($communities as $c) {
            if ($c['slug'] === $current_slug) { $current_community = $c; break; }
        }

        $cid = $current_community ? (int)$current_community['id'] : null;
        $posts = $postModel->getPostsForFeed($cid, $user_id ?? 0);

        foreach ($posts as &$post) {
            $post['comments'] = $postModel->getComments((int)$post['id']);
        }
        unset($post);

        $post_message = '';
        $this->view('community.index', compact(
            'user_id','current_slug','communities','user_communities',
            'current_community','posts','post_message'
        ));
    }

    public function handlePost(): void {
        \csrf_verify();
        $user_id      = $_SESSION['user_id'] ?? null;
        $current_slug = $_GET['c'] ?? 'all';
        if (!$user_id) { $this->redirect('/malath-php-app/login.php'); }

        $commModel = new CommunityModel();
        $postModel = new PostModel();
        $user_communities = $commModel->getUserCommunities($user_id);

        if (isset($_POST['join_community'])) {
            $commModel->join($user_id, intval($_POST['community_id']));
        } elseif (isset($_POST['leave_community'])) {
            $commModel->leave($user_id, intval($_POST['community_id']));
        } elseif (isset($_POST['submit_post'])) {
            $cid     = intval($_POST['post_community_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            $type    = $_POST['type'] ?? 'vent';
            $title   = trim($_POST['post_title'] ?? '');
            if ($cid && in_array($cid, $user_communities) && $content) {
                $postModel->create($user_id, $cid, $type, $title, $content);
            }
        }

        $this->redirect('/malath-php-app/community.php?c=' . urlencode($current_slug));
    }
}
