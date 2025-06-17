<?php

// controllers/ThreadController.php

class ThreadController extends BaseController
{
    private $thread_model;
    private $post_model;

    public function __construct()
    {
        parent::__construct();
        $this->post_model = $this->model('PostModel');
        $this->thread_model = $this->model('ThreadModel');
    }

    public function index($thread_id = null)
    {

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $offset = ($page - 1) * $GLOBALS['site_data']['site_posts_per_page'];

        $thread_id = sanitise_input($thread_id, 'int');

        if (is_null($thread_id)) {
            Helpers::url_redirect(url: "/nope");
        }

        $thread = $this->thread_model->get_thread($thread_id);

        if (!$thread) {
            Helpers::url_redirect(url: "/nope");
        }

        $public_params = [
            "thread_id" => $thread['thread_id'],
            "page" => $page ?? null
        ];

        $private_params = [
            "thread" => $thread,
            "limit" => $GLOBALS['site_data']['site_posts_per_page'],
            "offset" => $offset
        ];

        $posts = $this->post_model->get_posts_and_users_by_thread_id($public_params['thread_id'], $private_params['limit'], $private_params['offset']);

        $total_posts = $this->post_model->count_posts_by_thread_id($public_params['thread_id']);

        $total_pages = ceil($total_posts / $GLOBALS['site_data']['site_posts_per_page']);

        $private_params['total_posts'] = $total_posts;

        $private_params['total_pages'] = $total_pages;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action_data = [
                'action' => $_POST['action'] ?? null,
                'post_id' => $_POST['post_id'] ?? null
            ];

            $this->handle_thread($public_params, $private_params, $action_data);

        } elseif (isset($_GET['action'])) {

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

        $captcha = new Captcha(10, 300, 150, false, 'create_post');

        $cache = Flash::get("cache");

        $url = Helpers::url_builder("/thread/{$public_params['thread_id']}/", $public_params, ['thread_id']);

        $this->parse_posts_content($private_params['posts']);

        // Base

        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . $private_params['thread']['thread_title'];

        $this->render('Thread', [
            'public_params' => $public_params,
            'private_params' => $private_params,
            'url' => $url,
            'cache' => $cache,
            'captcha_html' => ($_SESSION['user_posts'] < 15) ? $captcha->render() : null
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

    private function handle_action($public_params, $private_params, $action_data)
    {
        $this->require_authentication();

        $action = sanitise_input($action_data['action'], 'text', '1', '255');

        if (is_null($action)) {
            Helpers::url_redirect(url: "/nope");
        }

        if (!is_null($action_data['post_id'])) {
            $post_id = sanitise_input($action_data['post_id'], 'int');
            if (is_null($post_id)) {
                Helpers::url_redirect(url: "/nope");
            }
            $post = $this->post_model->get_post($post_id);
            if (!$post) {
                Helpers::url_redirect(url: "/nope");
            }
        }

        $url = Helpers::url_builder("/thread/{$public_params['thread_id']}/", $public_params, ['thread_id']);

        switch ($action) {
            case 'lock_thread':
                $this->require_role('role_action_lock_thread');
                if ($this->thread_model->update_thread_row(['thread_lock' => 1], ['thread_id' => $public_params['thread_id']])) {
                    Helpers::url_redirect($url);
                }
                break;
            case 'unlock_thread':
                $this->require_role('role_action_lock_thread');
                if ($this->thread_model->update_thread_row(['thread_lock' => null], ['thread_id' => $public_params['thread_id']])) {
                    Helpers::url_redirect($url);
                }
                break;
            case 'pin_thread':
                $this->require_role('role_action_pin_thread');
                if ($this->thread_model->update_thread_row(['thread_pin' => 1], ['thread_id' => $public_params['thread_id']])) {
                    Helpers::url_redirect($url);
                }
                break;
            case 'unpin_thread':
                $this->require_role('role_action_pin_thread');
                if ($this->thread_model->update_thread_row(['thread_pin' => null], ['thread_id' => $public_params['thread_id']])) {
                    Helpers::url_redirect($url);
                }
                break;
            case 'delete_thread':
                $this->require_role('role_action_delete_thread');
                if ($this->thread_model->delete_thread($public_params['thread_id'])) {
                    Helpers::url_redirect(url: "/forum/{$private_params['thread']['thread_category']}");
                }
                break;
            case 'hide_post':
                $this->require_role('role_action_hide_post');
                if ($this->post_model->update_post_row(['post_hide' => 1], ['post_id' => $post_id])) {
                    Helpers::url_redirect(url: "$url#{$post_id}");
                }
                break;
            case 'unhide_post':
                $this->require_role('role_action_unhide_post');
                if ($this->post_model->update_post_row(['post_hide' => null], ['post_id' => $post_id])) {
                    Helpers::url_redirect(url: "$url#{$post_id}");
                }
                break;
            case 'delete_post':
                $this->require_role('role_action_delete_post');
                if ($this->post_model->delete_post($post_id)) {
                    Helpers::url_redirect(url: "$url#{$post_id}");
                }
                break;

            case 'quote_post':
                if ($post && $post['post_hide'] !== 1) {
                    $post_content = "> @" . $post['post_author'] . " :\n" . preg_replace('/^/m', '> ', $post['post_content']) . "\n\n";
                    Flash::set('cache', [
                        'CreatePost' => [
                            'post_content' => $post_content
                        ]
                    ]);
                }
                Helpers::url_redirect($url);
                break;

            case 'update_post':
                if ($post && $_SESSION['user_name'] === $post['post_author'] || $this->role_authorization('role_action_update_post')) {
                    $post_content = $post['post_content'];
                    Flash::set('cache', [
                        'CreatePost' => [
                            'post_content' => $post_content,
                            'action_data' => $action_data
                        ]
                    ]);
                }
                Helpers::url_redirect($url);
                break;

            default:
                Helpers::url_redirect(url: "/nope");
        }

    }

    private function handle_thread($public_params, $private_params, $action_data = null)
    {

        $this->require_authentication();

        $errors = [];

        if ($private_params['thread']['thread_lock'] == 1 && !$this->role_authorization('role_action_lock_thread')) {
            Helpers::url_redirect(url: "/nope");
        }

        $post_content = sanitise_input($_POST['post_content'], 'text', '4', '40000');

        if (is_null($post_content)) {
            $errors[] = Language::get('post_content_information');
        }

        $url = Helpers::url_builder("/thread/{$public_params['thread_id']}/", $public_params, ['thread_id']);

        if (empty($errors)) {

            if (isset($_POST['show_preview'])) {
                if (empty($errors)) {
                    Flash::set('cache', [
                        'CreatePost' => [
                            'post_content' => $post_content,
                            'action_data' => $action_data,
                            'preview' => [
                                'post_content_html' => Parsedown($_POST['post_content'], true, true, true, true)
                            ]
                        ]
                    ]);
                    Helpers::url_redirect(url: "$url#bottom");
                }
            }

            if (isset($_POST['add_sticker'])) {

                if (empty($errors)) {

                    $sticker_name = sanitise_input($_POST['add_sticker'], 'text', '1', '40');

                    if (is_null($sticker_name)) {
                        Helpers::url_redirect(url: "/nope");
                    }

                    Flash::set('cache', [
                        'CreatePost' => [
                            'post_content' => $post_content . ' ' . $sticker_name,
                            'action_data' => $action_data,
                        ]
                    ]);
                    Helpers::url_redirect(url: "$url#bottom");
                }
            }

            if (isset($_POST['create_post'])) {

                if ($_SESSION['user_posts'] < 15) {

                    $captcha = new Captcha(10, 300, 150, true, 'create_post');
                    
                    $buttonName = $captcha->getButtonName();
                    
                    $click_x = $_POST[$buttonName . '_x'] ?? null;
                    $click_y = $_POST[$buttonName . '_y'] ?? null;
                    
                    if ($click_x === null || $click_y === null || !$captcha->verify($click_x, $click_y)) {
                        $errors[] = Language::get('incorrect_captcha');
                    }
                }

                $rate_limit = $this->is_rate_limited('thread', 15, 60);

                if (!is_null($rate_limit)) {
                    Flash::set('cache', [
                        'CreatePost' => [
                            'post_content' => $post_content,
                            'action_data' => $action_data
                        ]
                    ]);
                    $errors[] = $rate_limit;
                }

                if ($this->is_black_listed($post_content)) {
                    Helpers::url_redirect(url: "/nope");
                }

                if ($action_data['action'] == 'update_post' && !is_null(sanitise_input($action_data['post_id'], 'int'))) {
                    $update_post = $this->post_model->get_post($action_data['post_id']);
                    if ($update_post) {
                        if ($update_post['post_author'] !== $_SESSION['user_name'] && !$this->role_authorization('role_action_update_post')) {
                            Helpers::url_redirect(url: "/nope");
                        } elseif (time() - $update_post['post_last_change_timestamp'] > 7200 && !$this->role_authorization('role_action_update_post')) {
                            $errors[] = Language::get('update_time_exceeded');
                        }
                    } else {
                        Helpers::url_redirect(url: "/nope");
                    }
                }

                if (empty($errors)) {

                    $db = Database::get_instance()->get_connection();

                    try {

                        $db->beginTransaction();

                        $timestamp = time();

                        if ($action_data['action'] === 'update_post' && !is_null(sanitise_input($action_data['post_id'], 'int'))) {

                            $update_post = $this->post_model->update_post_row(
                                ['post_content' => $post_content, 'post_last_change_timestamp' => $timestamp],
                                ['post_id' => $action_data['post_id']]
                            );

                            if ($update_post) {

                                $db->commit();

                                Flash::set('successes', ['successes' => Language::get('update_success')]);

                                Helpers::url_redirect(url: "$url#top");
                            }

                        } else {

                            $post_id = $this->post_model->create_post([
                                'post_thread_id' => $public_params['thread_id'],
                                'post_content' => $post_content,
                                'post_author' => $_SESSION['user_name'],
                                'post_timestamp' => $timestamp,
                                'post_last_change_timestamp' => $timestamp,
                                'post_hide' => 0
                            ]);

                            if ($post_id) {

                                $update_thread = $this->thread_model->update_thread_last_post([
                                    'thread_id' => $public_params['thread_id'],
                                    'thread_last_post_timestamp' => $timestamp,
                                    'thread_last_post_author' => $_SESSION['user_name'],
                                    'thread_last_post_id' => $post_id
                                ]);

                                if ($update_thread) {

                                    $db->commit();

                                    $total_pages = ceil(($private_params['total_posts'] + 1) / $GLOBALS['site_data']['site_posts_per_page']);

                                    Flash::set('successes', ['successes' => Language::get('post_success')]);

                                    Helpers::url_redirect(url: "$url#$post_id");

                                } else {

                                    throw new Exception(Language::get('thread_update_failed'));

                                }
                            } else {

                                throw new Exception(Language::get('thread_post_creation_failed'));
                            }
                        }
                    } catch (Exception $e) {

                        $db->rollBack();

                        exit();

                    }
                }

            }
        }

        $total_posts = $this->post_model->count_posts_by_thread_id($public_params['thread_id']);

        $total_pages = ceil($total_posts / $GLOBALS['site_data']['site_posts_per_page']);

        $public_params['total_posts'] = $total_posts;

        $public_params['total_pages'] = $total_pages;

        Flash::set('cache', [
            'CreatePost' => [
                'post_content' => $post_content,
                'action_data' => $action_data
            ]
        ]);

        Flash::set('errors', ['errors' => $errors]);

        Helpers::url_redirect(url: "$url#top");

    }

}
