<?php

// controllers/ProfileController.php

class ProfileController extends BaseController
{
    private $management_model;
    private $account_model;
    private $post_model;

    public function __construct()
    {
        parent::__construct();
        $this->management_model = $this->model('ManagementModel');
        $this->account_model = $this->model('AccountModel');
        $this->post_model = $this->model('PostModel');
    }

    public function index($user_name = null)
    {
        if (is_null($user_name) && $this->is_authenticated()) {

            $user = $this->base_model->get_user_by_user_name($_SESSION['user_name']);

            if ($user) {

                $this->profile_render($user);

            }

        } else {

            $user_name = sanitise_input($user_name, 'text', '3', '25');

            if (is_null($user_name)) {
                Helpers::url_redirect(url: "/nope");
            }

            $user = $this->base_model->get_user_by_user_name($user_name);

            if ($user) {

                if (isset($_GET['action'])) {
                    $this->handle_action([
                        'user_name' => $user_name,
                        'action' => $_GET['action']
                    ]);
                }

                $user_posts = $this->post_model->get_posts_by_user_name($user_name, $GLOBALS['site_data']['site_posts_per_profile']);

                $total_posts = $this->post_model->get_posts_by_user_name($user_name, null, true);

                foreach ($user_posts as &$post) {
                    $position = $this->post_model->get_post_position_by_thread_id($post['post_thread_id'], $post['post_id']);
                    $post['post_page'] = ceil($position / $GLOBALS['site_data']['site_posts_per_page']);
                }

                $user['user_description_html'] = Parsedown($user['user_description'], true, true, true);

                $this->profile_render($user, $user_posts, $total_posts);

            } else {

                $errors[] = Language::get('user_not_found');

                Flash::set('errors', ['errors' => $errors]);

                Helpers::url_redirect(url: "/forum");
            }

        }

    }

    private function profile_render($user = null, $user_posts = null, $total_posts = null)
    {
        $user['user_description_html'] = Parsedown($user['user_description'], true, true, true);

        $this->parse_posts_content($user_posts);

        // Base

        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . $user['user_name'];

        $this->render('Profile', [
            'user' => $user,
            'user_posts' => $user_posts,
            'total_posts' => $total_posts
        ]);

        exit();
    }

    private function parse_posts_content(&$posts)
    {
        foreach ($posts as &$post) {
            if (isset($post['post_content']) && $post['post_hide'] === 1) {
                $post['post_content_html'] = '<h4 style="color: red;">' . Language::get('post_hidden') . '</h4>';
            } else {
                if (isset($post['post_content'])) {
                    $post['post_content_html'] = Parsedown($post['post_content'], true, true, true);
                }
            }
        }
    }

    // Handle Action on Profile
    private function handle_action($action_data)
    {

        $user_name = sanitise_input($action_data['user_name'], 'text', '3', '25');

        if (is_null($user_name)) {
            Helpers::url_redirect(url: "/nope");
        }

        $action = sanitise_input($action_data['action'], 'text', '1', '255');

        if (is_null($action)) {
            Helpers::url_redirect(url: "/nope");
        }

        $user = $this->base_model->get_user_by_user_name($user_name);

        if ($user) {
            switch ($action) {
                case 'ban':
                    $this->require_role('role_action_ban');
                    if ($this->management_model->update_user_row(['user_ban' => 1], ['user_name' => $user_name])) {
                        Helpers::url_redirect(url: "/profile/{$user_name}");
                    }
                    break;
                case 'unban':
                    $this->require_role('role_action_ban');
                    if ($this->management_model->update_user_row(['user_ban' => null], ['user_name' => $user_name])) {
                        Helpers::url_redirect(url: "/profile/{$user_name}");
                    }
                    break;
                case 'delete_user':
                    $this->require_role('role_action_delete_user');
                    if ($this->management_model->delete_user($user_name)) {
                        if (remove_directory("./cloud/users/{$user['user_id']}/")) {
                            $this->management_model->delete_sqlite_sequence();
                            Helpers::url_redirect(url: "/forum");
                        }
                    }
                    break;
                case 'delete_user_posts':
                    $this->require_role('role_action_delete_user_posts');
                    if ($this->management_model->delete_user_posts($user_name)) {
                        $this->management_model->delete_sqlite_sequence();
                        Helpers::url_redirect(url: "/profile/{$user_name}");
                    }
                    break;
                case 'delete_user_threads':
                    $this->require_role('role_action_delete_user_threads');
                    if ($this->management_model->delete_user_threads($user_name)) {
                        $this->management_model->delete_sqlite_sequence();
                        Helpers::url_redirect(url: "/profile/{$user_name}");

                    }
                    break;
                case 'delete_user_description':
                    $this->require_role('role_action_delete_user_description');
                    if ($this->management_model->update_user_row(['user_description' => '**FORBIDDEN DESCRIPTION**'], ['user_name' => $user_name])) {
                        Helpers::url_redirect(url: "/profile/{$user_name}");
                    }
                    break;
                case 'delete_user_avatar':
                    $this->require_role('role_action_delete_user_avatar');
                    $avatar_dir = "cloud/users/{$user['user_id']}/avatar/";
                    if (!is_dir($avatar_dir)) {
                        mkdir($avatar_dir, 0755, true);
                    }
                    $user_avatar = generate_random_image($user_name);
                    file_put_contents($avatar_dir . "avatar.webp", $user_avatar['avatar.webp']);
                    file_put_contents($avatar_dir . "avatar-min.webp", $user_avatar['avatar-min.webp']);
                    Helpers::url_redirect(url: "/profile/{$user_name}");
                    break;
                default:
                    Helpers::url_redirect(url: "/nope");
            }

        }

    }

}
