<?php

// controllers/BaseController.php

class BaseController extends Controller
{
    protected $base_model;
    private $post_model;
    public function __construct()
    {
        $this->base_model = $this->model('BaseModel');
        $this->post_model = $this->model('PostModel');

        // GLOBALS 

        if (!isset($GLOBALS['site_data'])) {

            // Visits Sessions

            $redis = RedisClient::get_instance();

            if (!isset($_SESSION['user_visit'])) {

                $redis->incr('site_visits');

                if ($redis->ttl('site_visits') === -1) {
                    $redis->expire('site_visits', 86400);
                }

                $_SESSION['user_visit'] = true;

            }

            $site_visits = $redis->get('site_visits');

            // ---

            $site_data = $this->base_model->get_site_data();

            if ($site_data) {
                $GLOBALS['site_data'] = $site_data;
                $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_name'];
                $GLOBALS['site_data']['site_visits'] = $site_visits;
            } else {
                $GLOBALS['site_data'] = null;
            }
        }

        if (!isset($GLOBALS['statistics_data'])) {
            $threshold = time() - 7200;
            $statistics_data = $this->base_model->get_statistics_data($threshold);
            if ($statistics_data) {
                $GLOBALS['statistics_data'] = $statistics_data;
            } else {
                $GLOBALS['statistics_data'] = null;
            }
        }

        if (!isset($GLOBALS['stickers_data'])) {
            $stickers_data = $this->base_model->get_stickers_data();
            if ($stickers_data) {
                $GLOBALS['stickers_data'] = $stickers_data;
            } else {
                $GLOBALS['stickers_data'] = null;
            }
        }

        // CSRF VALIDATION

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                Helpers::url_redirect(url: "/nope");
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['action'])) {
                if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
                    Helpers::url_redirect(url: "/nope");
                }
            }
        }

        // SESSION & BAN

        if ($this->is_authenticated()) {
            $user = $this->base_model->get_user_by_user_name($_SESSION['user_name']);
            $user_posts = $this->post_model->get_posts_by_user_name($_SESSION['user_name'], null, true);
            if ($user && isset($user['user_ban']) && $user['user_ban'] === 1) {
                $this->session_destroy('/unauthorised');
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['user_action_timestamp'] = $user['user_action_timestamp'];
                $_SESSION['user_kick_timestamp'] = $user['user_kick_timestamp'];
                $_SESSION['user_posts'] = $user_posts;

                $_SESSION['user_language'] = $user['user_language'];
                
                Language::load($user['user_language']);
            }

            if (time() - $_SESSION['user_action_timestamp'] > 7200) {
                $this->base_model->update_user_action_timestamp($_SESSION['user_name'], time());
            }

        } else {

            $lang = substr(Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en'), 0, 2);

            Language::load($lang);

        }

    }

    protected function render($view, $data = [], $is_partial = false)
    {
        if (isset($_SESSION['flash'])) {
            foreach ($_SESSION['flash'] as $key => $message) {
                $data[$key] = $message;
            }
        }

        $data = array_merge([
            'site_data' => $GLOBALS['site_data'],
            'statistics_data' => $GLOBALS['statistics_data'],
            'stickers_data' => $GLOBALS['stickers_data'],
            'user_authenticated' => $this->is_authenticated(),
            'role_authorization' => $this->role_authorization(),
            'csrf_token' => $_SESSION['csrf_token']
        ], $data);

        $this->view($view, $data, $is_partial);
    }

    protected function is_rate_limited($action, $max_requests, $window_seconds)
    {

        $current_time = time();

        $rate_limit_key = $action . '_rate_limit';

        if (!isset($_SESSION[$rate_limit_key])) {
            $_SESSION[$rate_limit_key] = [
                'count' => 0,
                'window_start' => $current_time
            ];
        }

        $rate_data = &$_SESSION[$rate_limit_key];

        if ($current_time - $rate_data['window_start'] > $window_seconds) {
            $rate_data['count'] = 0;
            $rate_data['window_start'] = $current_time;
        }

        $rate_data['count']++;

        if ($rate_data['count'] > $max_requests) {
            $remaining_time = $window_seconds - ($current_time - $rate_data['window_start']);
            if ($remaining_time < 0) {
                $remaining_time = 0;
            }
            return "Please wait {$remaining_time} seconds before trying again.";
        }

        return null;
    }

    protected function is_black_listed($text)
    {
        $black_list_array = $this->base_model->get_black_list();
        foreach ($black_list_array as $black_list_sample) {
            if (isset($black_list_sample['black_list_term']) && stripos((string) $text, $black_list_sample['black_list_term']) !== false) {
                return true;
            }
        }
    }

    protected function is_authenticated()
    {
        return isset($_SESSION['user_id']);
    }

    protected function require_authentication()
    {
        if (!$this->is_authenticated()) {
            Helpers::url_redirect("/signin");
        }
    }

    protected function require_unauthentication()
    {
        if ($this->is_authenticated()) {
            Helpers::url_redirect(url: "/nope");
        }
    }

    protected function session_destroy($redirection)
    {
        session_unset();
        session_destroy();
        Helpers::url_redirect("$redirection");
    }

    protected function role_authorization($action = null)
    {
        if ($this->is_authenticated()) {
            $roles = $this->base_model->get_roles();
            $user_role = $_SESSION['user_role'] ?? null;
            if ($user_role && !empty($roles)) {
                foreach ($roles as $role) {
                    if (isset($role['role_id']) && $role['role_id'] == $user_role) {
                        if ($action !== null) {
                            return array_key_exists($action, $role) && $role[$action] == 1;
                        }
                        return $role;
                    }
                }
            }
        }
        return $action !== null ? false : null;
    }

    protected function require_role($role_action)
    {
        if ($this->require_authentication()) {
            Helpers::url_redirect(url: "/nope");
        }

        if (!$this->role_authorization($role_action)) {
            Helpers::url_redirect(url: "/nope");
        }

        return true;
    }

    protected function get_languages(string $dir = 'languages'): array
    {

        $labelsFile = $dir . DIRECTORY_SEPARATOR . 'core.php';

        $labels = is_file($labelsFile) ? require $labelsFile : [];

        if (!is_dir($dir)) {
            return [];
        }

        $langs = [];
        foreach (scandir($dir) as $entry) {

            if ($entry[0] === '.' || !preg_match('/^[a-z]{2}(?:_[A-Z]{2})?\.php$/', $entry)) {
                continue;
            }

            $code = pathinfo($entry, PATHINFO_FILENAME);

            $langs[$code] = $labels[$code] ?? $code;
        }

        return $langs;     
    }

    protected function is_language(string $code, string $dir = 'languages'): bool
    {
        return array_key_exists($code, $this->get_languages($dir));
    }

}