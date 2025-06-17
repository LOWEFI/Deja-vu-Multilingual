<?php

// controllers/SignupController.php

class SignupController extends BaseController
{
    private $signup_model;

    public function __construct()
    {
        parent::__construct();
        $this->signup_model = $this->model('SignupModel');
    }

    public function index()
    {
        $this->require_unauthentication();

        if ($GLOBALS['site_data']['site_registration'] === 0) {

            Flash::set('errors', ['errors' => Language::get('closed_registration_information')]);

            Helpers::url_redirect(url: "/signin");

        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_signup();
        } else {
            $this->signup_render();
        }
    }

    // Render

    private function signup_render()
    {
        $captcha = new Captcha();

        $languages = $this->get_languages();

        // Base

        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . Language::get('sign_up');

        $this->render('Signup', [
            'languages' => $languages,
            'captcha_html' => $captcha->render()
        ]);

        exit();
    }

    // Handle

    private function handle_signup()
    {
        $this->require_unauthentication();

        if ($GLOBALS['site_data']['site_registration'] === 0) {

            Flash::set('errors', ['errors' => Language::get('closed_registration_information')]);

            Helpers::url_redirect(url: "/signin");

        }

        $errors = [];

        // Captcha
        // ----------
        // $captcha = new Captcha(isVerification: true);
        // $selectedTiles = $_POST['captcha_tiles'] ?? [];
        // if (!$captcha->verify($selectedTiles)) {
        //    $errors[] = Language::get('incorrect_captcha');
        //    Flash::set('errors', ['errors' => $errors]);
        //    Helpers::url_redirect(url: "/signup");
        // }
        // ----------

        $captcha = new Captcha(is_verification: true);

        $click_x = $_POST['captcha_click_x'] ?? null;
        $click_y = $_POST['captcha_click_y'] ?? null;

        if ($click_x === null || $click_y === null || !$captcha->verify($click_x, $click_y)) {
            $errors[] = Language::get('incorrect_captcha');
            Flash::set('errors', ['errors' => $errors]);
            Helpers::url_redirect(url: "/signin");
        }

        $rate_limit = $this->is_rate_limited('signup', 5, 60);

        if (!is_null($rate_limit)) {
            Helpers::url_redirect(url: "/signup");
        }

        $user_name = sanitise_input($_POST['user_name'], 'text', '3', '25');

        if (
            is_null($user_name)
            || !ctype_alnum($user_name)
        ) {
            $errors[] = Language::get('user_name_information');
        }

        $user_password = sanitise_input($_POST['user_password'], 'text', '6', '65');

        $user_confirm_password = sanitise_input($_POST['user_confirm_password'], 'text', '6', '65');

        if ($user_password !== $user_confirm_password) {
            $errors[] = Language::get('user_passwords_mismatch');
        }

        if (
            is_null($user_password)
            || is_null($user_confirm_password)
            || !preg_match('(^[A-Za-z0-9!@#$%^&*()\-_=+\[\]{}|\\;:\'",./<>?~][A-Za-z0-9!@#$%^&*()\-_=+\[\]{}|\\;:\'",./<>?~]{0,63}[A-Za-z0-9!@#$%^&*()\-_=+\[\]{}|\\;:\'",./<>?~]$)', $user_password)
        ) {
            $errors[] = Language::get('user_password_information');
        }

        if ($this->base_model->get_user_by_user_name($user_name)) {
            $errors[] = Language::get('user_exists');
        }

        $user_language = sanitise_input($_POST['user_language'], 'text', '2', '2');

        if (
            is_null($user_language)
        ) {
            Helpers::url_redirect(url: "/nope");
        } else {
            if (!$this->is_language($user_language)) {
                Helpers::url_redirect(url: "/nope");
            }
        }

        if (empty($errors)) {

            $user_password = password_hash($user_password, PASSWORD_BCRYPT);

            $user_id = $this->signup_model->signup([
                'user_name_raw' => $user_name,
                'user_name' => strtolower($user_name),
                'user_password' => $user_password,
                'user_description' => 'Nuh uh!!',
                'user_role' => null,
                'user_ban' => null,
                'user_action_timestamp' => 0,
                'user_creation_timestamp' => time(),
                'user_kick_timestamp' => 0,
                'user_language' => $user_language
            ]);

            if ($user_id) {

                $user_dir = "cloud/users/{$user_id}";

                if (!is_dir($user_dir)) {

                    mkdir("cloud/users/$user_id", 0755, true);

                    $avatar_dir = "cloud/users/{$user_id}/avatar/";

                    if (!is_dir($avatar_dir)) {
                        mkdir($avatar_dir, 0755, true);
                    }

                    $user_avatar = generate_random_image($user_name);

                    file_put_contents($avatar_dir . "avatar.webp", $user_avatar['avatar.webp']);

                    file_put_contents($avatar_dir . "avatar-min.webp", $user_avatar['avatar-min.webp']);

                }

                Flash::set('successes', ['successes' => Language::get('sign_up_success')]);

                Helpers::url_redirect(url: "/signin");

            }

        }

        Flash::set('errors', ['errors' => $errors]);

        Helpers::url_redirect(url: "/signup");

    }
}
