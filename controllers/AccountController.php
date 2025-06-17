<?php

// controllers/AccountController.php

class AccountController extends BaseController
{
    private $account_model;

    public function __construct()
    {
        parent::__construct();
        $this->account_model = $this->model('AccountModel');
    }

    public function index()
    {
        $this->require_authentication();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->handle_account();

        } elseif (isset($_GET['action'])) {

            $this->handle_action(['action' => $_GET['action']]);

        } else {

            $user = $this->base_model->get_user_by_user_name($_SESSION['user_name']);

            if ($user) {

                $this->account_render($user);

            }

        }
    }

    // Handle Action on Account

    private function handle_action($action_data)
    {

        $action = sanitise_input($action_data['action'], 'text', '1', '255');

        if (is_null($action)) {
            Helpers::url_redirect(url: "/nope");
        }

        switch ($action) {
            case 'signout':
                Flash::set('successes', ['successes' => Language::get('sign_out_success')]);
                $this->session_destroy('/signin');
                break;
            default:
                Helpers::url_redirect(url: "/nope");
        }

    }

    // Render

    private function account_render($user = null)
    {
        $user['user_description_html'] = Parsedown(
            $user['user_description'],
            true,
            true,
            true,
            true
        );

        $languages = $this->get_languages();

        // Base

        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . Language::get('account');

        $this->render('Account', [
            'languages' => $languages,
            'user' => $user
        ]);

        exit();
    }

    // Handle

    private function handle_account()
    {
        $this->require_authentication();

        $errors = [];

        $rate_limit = $this->is_rate_limited('account', 15, 60);

        if (!is_null($rate_limit)) {

            Flash::set('errors', ['errors' => $rate_limit]);

            Helpers::url_redirect(url: "/account");

        }

        $user = $this->base_model->get_user_by_user_name($_SESSION['user_name']);

        // Update Account Password

        if (isset($_POST['update_account_language'])) {

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

            if ($this->account_model->update_account($_SESSION['user_name'], ['user_language' => $user_language])) {

                Flash::set('successes', ['successes' => Language::get('update_success')]);

                Helpers::url_redirect(url: "/account");

            }

        }

        // Update Account Password

        if (isset($_POST['update_account_password'])) {

            $user_password = sanitise_input($_POST['user_password'], 'text', '6', '65');

            $user_new_password = sanitise_input($_POST['user_new_password'], 'text', '6', '65');

            $user_confirm_new_password = sanitise_input($_POST['user_confirm_new_password'], 'text', '6', '65');

            if ($user_new_password !== $user_confirm_new_password) {
                $errors[] = Language::get('user_passwords_mismatch');
            }

            if (
                is_null($user_password)
                || is_null($user_new_password)
                || is_null($user_confirm_new_password)
                || !preg_match('(^[A-Za-z0-9!@#$%^&*()\-_=+\[\]{}|\\;:\'",./<>?~][A-Za-z0-9!@#$%^&*()\-_=+\[\]{}|\\;:\'",./<>?~]{0,63}[A-Za-z0-9!@#$%^&*()\-_=+\[\]{}|\\;:\'",./<>?~]$)', $user_new_password)
            ) {
                $errors[] = Language::get('user_password_information');
            }

            if (empty($errors)) {

                if ($user && password_verify($user_password, $user['user_password'])) {

                    $user_password = password_hash($user_new_password, PASSWORD_BCRYPT);

                    if ($this->account_model->update_account($_SESSION['user_name'], ['user_password' => $user_password])) {

                        Flash::set('successes', ['successes' => Language::get('update_success')]);

                        Helpers::url_redirect(url: "/account");

                    }

                } else {

                    $errors[] = Language::get('incorrect_password');

                }
            }
        }

        // Update Account Description 

        if (isset($_POST['update_account_description'])) {

            $user_description = sanitise_input($_POST['user_description'], 'text', '1', '5000');

            if (is_null($user_description)) {
                $errors[] = Language::get('description_information');
            }

            if (empty($errors)) {

                if ($this->is_black_listed($user_description)) {
                    Helpers::url_redirect(url: "/nope");
                }

                if ($this->account_model->update_account($_SESSION['user_name'], ['user_description' => $user_description])) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/account");

                }
            }

        }

        // Update Account Avatar

        if (isset($_POST['update_account_avatar'])) {

            $user_avatar = sanitise_avatar($_FILES['user_avatar']);

            if (is_null($user_avatar)) {
                $errors[] = Language::get('image_information');
            }

            if (empty($errors)) {

                $avatar_dir = "cloud/users/{$_SESSION['user_id']}/avatar/";

                if (!is_dir($avatar_dir)) {
                    mkdir($avatar_dir, 0755, true);
                }

                file_put_contents($avatar_dir . "avatar.webp", $user_avatar['avatar.webp']);

                file_put_contents($avatar_dir . "avatar-min.webp", $user_avatar['avatar-min.webp']);

                Flash::set('successes', ['successes' => Language::get('update_success')]);

                Helpers::url_redirect(url: "/account");

            }

        }

        Flash::set('errors', ['errors' => $errors]);

        Helpers::url_redirect(url: "/account");

    }
}

