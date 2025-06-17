<?php

// controllers/ForumController.php

class ForumController extends BaseController
{
    private $post_model;
    private $thread_model;
    private $forum_model;

    public function __construct()
    {
        parent::__construct();
        $this->post_model = $this->model('PostModel');
        $this->thread_model = $this->model('ThreadModel');
        $this->forum_model = $this->model('ForumModel');
    }

    public function index($category_id = null)
    {

        if (!is_null($category_id)) {

            $category_id = sanitise_input($category_id, 'int');

            if (is_null($category_id)) {
                Helpers::url_redirect(url: "/nope");
            }

        }

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

        $offset = ($page - 1) * $GLOBALS['site_data']['site_threads_per_page'];

        $public_params = [
            "category_id" => $category_id ?? null,
            "page" => $page ?? null,
        ];

        $private_params = [
            "limit" => $GLOBALS['site_data']['site_threads_per_page'],
            "offset" => $offset
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (is_null($category_id)) {

                $public_params['category_id'] = 1;

            }

            $this->handle_thread($public_params, $private_params);

        } elseif (isset($_GET['search']) && !is_null($_GET['search'])) {

            if (isset($_GET['search']) && !is_null($_GET['search'])) {

                $search = sanitise_input($_GET['search'], 'text', '1', '70');

                if (is_null($search)) {

                    Helpers::url_redirect(url: "/nope");

                }

            }

            $public_params['search'] = $_GET['search'];

            $this->search($public_params, $private_params);

        } else {

            if (isset($_GET['language'])) {

                $language = sanitise_input($_GET['language'], 'text', '2', '2');

                if (!is_null($language)) {

                    if ($this->is_language($_GET['language'])) {

                        $categories = $this->forum_model->get_categories($language);

                    } else {

                        Helpers::url_redirect(url: "/nope");
                        
                    }
                }

            } else {

                if (isset($_SESSION['user_language'])) {

                    $categories = $this->forum_model->get_categories($_SESSION['user_language']);

                } else {

                    $categories = $this->forum_model->get_categories();

                }

            }

            if (count($categories) == 1) {

                $public_params['category_id'] = $categories[0]['category_id'];

                $this->category($public_params, $private_params);

            } elseif ($public_params['category_id']) {

                $this->category($public_params, $private_params);

            } else {

                $private_params['category_id'] = $category ?? null;

                $private_params['categories'] = $categories ?? null;

                $this->forum_render($public_params, $private_params);

            }

        }

    }

    private function search($public_params, $private_params)
    {

        $url = Helpers::url_builder("/forum/{$public_params['category_id']}/", $public_params, ['category_id']);

        $rate_limit = $this->is_rate_limited('search', 30, 60);

        if (!is_null($rate_limit)) {

            Flash::set('errors', ['errors' => $rate_limit]);

            Helpers::url_redirect("/forum/{$public_params['category_id']}/");

        }

        $category = $this->forum_model->get_category($public_params['category_id']);

        if (!$category) {
            Helpers::url_redirect(url: "/nope");
        }

        $app_params = array_merge($public_params, $private_params);

        $total_threads = $this->thread_model->get_threads($app_params, true);

        $threads = $this->thread_model->get_threads($app_params);

        if (!$threads) {

            Flash::set('errors', ['errors' => Language::get('no_search_results')]);

            Helpers::url_redirect("/forum/{$public_params['category_id']}");

        }

        $private_params['threads'] = $threads;

        $private_params['category'] = $category;

        // Cal

        $total_pages = ceil($total_threads / $GLOBALS['site_data']['site_threads_per_page']);

        $private_params['total_pages'] = $total_pages;

        $this->forum_render($public_params, $private_params);

    }

    private function category($public_params, $private_params)
    {

        $category = $this->forum_model->get_category($public_params['category_id']);

        if (!$category) {
            Helpers::url_redirect(url: "/nope");
        }

        $total_threads = $this->thread_model->get_threads($public_params, true);

        $threads = $this->thread_model->get_threads([
            'category_id' => $public_params['category_id'],
            'limit' => $private_params['limit'],
            'offset' => $private_params['offset']
        ]);

        $private_params['category'] = $category;

        $private_params['threads'] = $threads;

        $total_pages = ceil($total_threads / $GLOBALS['site_data']['site_threads_per_page']);

        $private_params['total_pages'] = $total_pages;

        $this->forum_render($public_params, $private_params);
    }

    private function forum_render($public_params, $private_params)
    {

        $captcha = new Captcha(10, 300, 150, false, 'create_thread');

        $cache = Flash::get("cache");

        $url = Helpers::url_builder("/forum/{$public_params['category_id']}/", $public_params, ['category_id']);

        // Base

        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . $private_params['category']['category_name'];

        $this->render('Forum', [
            'public_params' => $public_params,
            'private_params' => $private_params,
            'url' => $url,
            'cache' => $cache,
            'captcha_html' => ($_SESSION['user_posts'] < 15) ? $captcha->render() : null
        ]);

        exit();
    }

    private function handle_thread($public_params, $private_params)
    {

        $this->require_authentication();

        $errors = [];

        $category = $this->forum_model->get_category($public_params['category_id']);

        if (!$category) {
            Helpers::url_redirect(url: "/nope");
        }

        $thread_title = sanitise_input($_POST['thread_title'], 'text', 4, 70);

        if (is_null($thread_title)) {
            $errors[] = Language::get('thread_title_information');
        }

        $post_content = sanitise_input($_POST['post_content'], 'text', 4, 40000);

        if (is_null($post_content)) {
            $errors[] = Language::get('post_content_information');
        }

        if (empty($errors)) {

            if (isset($_POST['show_preview'])) {

                Flash::set('cache', [
                    'CreateThread' => [
                        'thread_title' => $thread_title,
                        'post_content' => $post_content,
                        'preview' => [
                            'thread_title' => $thread_title,
                            'post_content_html' => Parsedown(
                                $post_content,
                                true,
                                true,
                                true,
                                true
                            )
                        ]
                    ]
                ]);

                Helpers::url_redirect(url: "/forum/{$public_params['category_id']}/#bottom");

            }

            if (isset($_POST['add_sticker'])) {

                if (empty($errors)) {

                    $sticker_name = sanitise_input($_POST['add_sticker'], 'text', '1', '40');

                    if (is_null($sticker_name)) {
                        Helpers::url_redirect(url: "/nope");
                    }

                    Flash::set('cache', [
                        'CreateThread' => [
                            'thread_title' => $thread_title,
                            'post_content' => $post_content . ' ' . $sticker_name,
                        ]
                    ]);
                    Helpers::url_redirect(url: "/forum/{$public_params['category_id']}/#bottom");
                }
            }

            if (isset($_POST['create_thread'])) {

                if ($_SESSION['user_posts'] < 15) {

                    $captcha = new Captcha(10, 300, 150, true, 'create_thread');

                    $buttonName = $captcha->getButtonName();

                    $click_x = $_POST[$buttonName . '_x'] ?? null;
                    $click_y = $_POST[$buttonName . '_y'] ?? null;

                    if ($click_x === null || $click_y === null || !$captcha->verify($click_x, $click_y)) {
                        $errors[] = Language::get('incorrect_captcha');
                    }
                }

                $rate_limit = $this->is_rate_limited('thread', 2, 60);

                if (!is_null($rate_limit)) {

                    Flash::set('cache', [
                        'CreateThread' => [
                            'thread_title' => $thread_title,
                            'post_content' => $post_content,
                        ]
                    ]);

                    $errors[] = $rate_limit;
                }

                if ($this->is_black_listed($thread_title) || $this->is_black_listed($post_content)) {
                    Helpers::url_redirect(url: "/nope");
                }

                if (empty($errors)) {

                    $db = Database::get_instance()->get_connection();

                    try {

                        $db->beginTransaction();

                        $timestamp = time();

                        $thread_id = $this->thread_model->create_thread([
                            'thread_title' => $thread_title,
                            'thread_author' => $_SESSION['user_name'],
                            'thread_pin' => null,
                            'thread_lock' => null,
                            'thread_category' => $public_params['category_id']
                        ]);

                        if ($thread_id) {

                            $post_id = $this->post_model->create_post([
                                'post_thread_id' => $thread_id,
                                'post_content' => $post_content,
                                'post_author' => $_SESSION['user_name'],
                                'post_timestamp' => $timestamp,
                                'post_last_change_timestamp' => $timestamp,
                                'post_hide' => null
                            ]);

                            if ($post_id) {

                                $update_thread = $this->thread_model->update_thread_last_post([
                                    'thread_id' => $thread_id,
                                    'thread_last_post_author' => $_SESSION['user_name'],
                                    'thread_last_post_timestamp' => $timestamp,
                                    'thread_last_post_id' => $post_id
                                ]);

                                if ($update_thread) {

                                    $db->commit();

                                    Helpers::url_redirect(url: "/thread/$thread_id");

                                } else {

                                    throw new Exception(Language::get('thread_update_failed'));

                                }
                            } else {

                                throw new Exception(Language::get('post_creation_failed'));

                            }
                        } else {

                            throw new Exception(Language::get('thread_creation_failed'));

                        }
                    } catch (Exception $e) {

                        $db->rollBack();

                        exit();
                    }
                }
            }

        }

        Flash::set('cache', [
            'CreateThread' => [
                'thread_title' => $thread_title,
                'post_content' => $post_content,
            ]
        ]);

        Flash::set('errors', ['errors' => $errors]);

        Helpers::url_redirect(url: "/forum/{$public_params['category_id']}/");

    }

}
