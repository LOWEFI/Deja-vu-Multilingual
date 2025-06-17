<?php

// controllers/PostsController.php

class PostsController extends BaseController
{
    private $thread_model;
    private $post_model;

    public function __construct()
    {
        parent::__construct();
        $this->post_model = $this->model('PostModel');
        $this->thread_model = $this->model('ThreadModel');
    }

    public function index()
    {

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $offset = ($page - 1) * $GLOBALS['site_data']['site_posts_per_page'];

        $public_params = [
            "page" => $page ?? null
        ];

        $private_params = [
            "limit" => $GLOBALS['site_data']['site_posts_per_page'],
            "offset" => $offset
        ];

        $posts = $this->post_model->get_posts_and_users($private_params['limit'], $private_params['offset']);

        $total_posts = $this->post_model->count_posts();

        $total_pages = ceil($total_posts / $GLOBALS['site_data']['site_posts_per_page']);

        foreach ($posts as &$post) {
            $position = $this->post_model->get_post_position_by_thread_id($post['post_thread_id'], $post['post_id']);
            $post['post_page'] = ceil($position / $GLOBALS['site_data']['site_posts_per_page']);
        }

        $private_params['total_posts'] = $total_posts;

        $private_params['total_pages'] = $total_pages;

        if (isset($_GET['action'])) {

            $action_data = [
                'action' => $_GET['action'] ?? null,
                'post_id' => $_GET['post_id'] ?? null
            ];

            $this->handle_action($public_params, $private_params, $action_data);

        } else {

            $private_params['posts'] = $posts;

            $this->thread_render($public_params, $private_params);

        }
    }

    private function thread_render($public_params, $private_params)
    {

        // $url = Helpers::url_builder("/posts/{$public_params['category_id']}/", $public_params, ['category_id']);

        $this->parse_posts_content($private_params['posts']);

        // Base
        
        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . Language::get('posts');

        $this->render('Posts', [
            'public_params' => $public_params,
            'private_params' => $private_params,
            // 'url' => $url,
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

}
