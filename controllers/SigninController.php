<?php

// controllers/SigninController.php

class SigninController extends BaseController
{

    //private $base_model;

    // public function __construct(){
    // parent::__construct();
    // $this->base_model = $this->model('BaseModel');
    // }

    public function index()
    {
        $this->require_unauthentication();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_signin();
        } else {
            $this->signin_render();
        }
    }

    // Render

    private function signin_render()
    {
        $captcha = new Captcha();

        // Base
        
        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . Language::get('sign_in');

        $this->render('Signin', [
            'captcha_html' => $captcha->render()
        ]);

        exit();
    }

    // Handle

    private function handle_signin()
    {
        $this->require_unauthentication();

        $errors = [];

        // Captcha
        // ----------
        // $captcha = new Captcha(isVerification: true);
        // $selectedTiles = $_POST['captcha_tiles'] ?? [];
        // if (!$captcha->verify($selectedTiles)) {
        //    $errors[] = Language::get('incorrect_captcha');
        //    Flash::set('errors', ['errors' => $errors]);
        //    Helpers::url_redirect(url: "/signin");
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

        $user_name = sanitise_input($_POST['user_name'], 'text', '3', '25');

        if (is_null($user_name)) {
            $errors[] = Language::get('user_name_information');
        }

        $user_password = sanitise_input($_POST['user_password'], 'text', '6', '65');

        if (is_null($user_password)) {
            $errors[] = Language::get('user_password_information');
        }

        if (empty($errors)) {

            $user = $this->base_model->get_user_by_user_name(strtolower($user_name));

            if ($user) {

                if (is_null($user['user_ban'])) {

                    if (password_verify($user_password, $user['user_password'])) {

                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_name'] = strtolower($user['user_name']);
                        $_SESSION['user_role'] = $user['user_role'];

                        if($this->base_model->update_user_action_timestamp($_SESSION['user_name'], time())){

                            Helpers::url_redirect(url: "/forum");

                        }

                    } else {
                        $errors[] = Language::get('incorrect_password');
                    }

                } else {

                    $errors[] = Language::get('banned_information');

                }

            } else {

                $errors[] = Language::get('user_not_found');

            }
        }

        Flash::set('errors', ['errors' => $errors]);

        Helpers::url_redirect(url: "/signin");

    }
}
