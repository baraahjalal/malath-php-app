<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class ProfileController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $user_id = (int)$_SESSION['user_id'];
        $model   = new UserModel();

        $user               = $model->findById($user_id);
        $contributions_count = $model->getContributionsCount($user_id);
        $saved_count        = $model->getSavedCount($user_id);
        $activities         = $model->getActivities($user_id);
        $saved_posts        = $model->getSavedPosts($user_id);

        $status = $_GET['status'] ?? '';
        $this->view('profile.index', compact(
            'user','contributions_count','saved_count','activities','saved_posts','status'
        ));
    }

    public function handlePostAction(): void {
        $this->requireAuth();
        \csrf_verify();
        $user_id  = (int)$_SESSION['user_id'];
        $model    = new UserModel();
        $postModel = new \App\Models\PostModel();

        if (isset($_POST['delete_post_id'])) {
            $postModel->deleteOwned((int)$_POST['delete_post_id'], $user_id);
        } elseif (isset($_POST['edit_post_id']) && isset($_POST['edit_content'])) {
            $postModel->updateContent((int)$_POST['edit_post_id'], $user_id, trim($_POST['edit_content']));
        }
        $this->redirect('/malath-php-app/profile.php');
    }

    public function handleUpdate(): void {
        $this->requireAuth();
        \csrf_verify();
        $user_id = (int)$_SESSION['user_id'];
        $model   = new UserModel();

        $name = trim($_POST['name'] ?? '');
        $bio  = trim($_POST['bio']  ?? '');

        $fields = ['name' => $name, 'bio' => $bio];

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg','jpeg','png'];
            $ext     = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true)) {
                $dir = ROOT_PATH . '/assets/uploads/avatars/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename   = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $target     = $dir . $filename;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                    $fields['avatar'] = 'assets/uploads/avatars/' . $filename;
                }
            }
        }

        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] === ($_POST['confirm_password'] ?? '')) {
                $fields['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            }
        }

        try {
            $model->update($user_id, $fields);
            $this->redirect('/malath-php-app/profile.php?status=updated');
        } catch (\PDOException $e) {
            error_log("Profile update: " . $e->getMessage());
            $this->redirect('/malath-php-app/profile.php?status=error');
        }
    }
}
