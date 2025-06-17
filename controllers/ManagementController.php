<?php

// controllers/ManagementController.php

class ManagementController extends BaseController
{

    private $management_model;
    private $forum_model;

    public function __construct()
    {
        parent::__construct();
        $this->forum_model = $this->model('ForumModel');
        $this->management_model = $this->model('ManagementModel');
    }

    public function index()
    {
        $this->require_role('role_management');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->handle_management_update();

        } else {

            $categories = $this->forum_model->get_categories();

            $black_list = $this->base_model->get_black_list();

            $users = $this->base_model->get_users();

            $roles = $this->base_model->get_roles();

            $users_number = count($users);

            $this->management_render($categories, $users, $roles, $users_number, $black_list);

        }
    }

    private function management_render($categories = null, $users = null, $roles = null, $users_number = null, $black_list = null)
    {

        $languages = $this->get_languages();

        // Base

        $GLOBALS['site_data']['site_title'] = $GLOBALS['site_data']['site_title'] . " - " . Language::get('management');

        $this->render('Management', [
            'languages' => $languages,
            'categories' => $categories,
            'users' => $users,
            'roles' => $roles,
            'users_number' => $users_number,
            'black_list' => $black_list
        ]);

        exit();

    }

    private function handle_management_update()
    {
        $this->require_role('role_management');

        $errors = [];

        if (isset($_POST['update_site'])) {

            $this->require_role('role_action_manage_site');

            $site_data = [];

            if (isset($_POST['site_name'])) {
                $site_data['site_name'] = sanitise_input($_POST['site_name'], 'text', '1', '35');
            }
            if (isset($_POST['site_description'])) {
                $site_data['site_description'] = sanitise_input($_POST['site_description'], 'text', '1', '255');
            }
            if (isset($_POST['site_keywords'])) {
                $site_data['site_keywords'] = sanitise_input($_POST['site_keywords'], 'text', '1', '510');
            }
            if (isset($_POST['site_information'])) {
                $site_data['site_information'] = sanitise_input($_POST['site_information'], 'text', '1', '255');
            }
            if (isset($_POST['site_registration'])) {
                $site_data['site_registration'] = sanitise_input($_POST['site_registration'], 'bool');
            }
            if (isset($_POST['site_chat'])) {
                $site_data['site_chat'] = sanitise_input($_POST['site_chat'], 'bool');
            }
            if (isset($_POST['site_threads_per_page'])) {
                $site_data['site_threads_per_page'] = sanitise_input($_POST['site_threads_per_page'], 'int');
            }
            if (isset($_POST['site_posts_per_page'])) {
                $site_data['site_posts_per_page'] = sanitise_input($_POST['site_posts_per_page'], 'int');
            }
            if (isset($_POST['site_posts_per_profile'])) {
                $site_data['site_posts_per_profile'] = sanitise_input($_POST['site_posts_per_profile'], 'int');
            }

            if (
                is_null($site_data['site_name'])
                || is_null($site_data['site_description'])
                || is_null($site_data['site_keywords'])
                || is_null($site_data['site_information'])
                || is_null($site_data['site_registration'])
                || is_null($site_data['site_chat'])
                || is_null($site_data['site_threads_per_page'])
                || is_null($site_data['site_posts_per_page'])
                || is_null($site_data['site_posts_per_profile'])
            ) {
                $errors[] = Language::get('incorrect_input');
            }

            if (empty($errors) && !empty($site_data)) {

                if ($this->management_model->update_site($site_data)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }

            }
        }

        if (isset($_POST['add_black_list'])) {

            $this->require_role('role_action_manage_black_list');

            $black_list_term = sanitise_input($_POST['black_list_term'], 'text', '1', '255');

            if (is_null($black_list_term)) {
                $errors[] = Language::get('black_list_information');
            }

            if (empty($errors)) {

                if ($this->management_model->add_black_list($black_list_term)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }

            }
        }

        if (isset($_POST['delete_black_list'])) {

            $this->require_role('role_action_manage_black_list');

            $black_list_term = sanitise_input($_POST['black_list_term'], 'text', '1', '255');

            if (is_null($black_list_term)) {
                $errors[] = Language::get('black_list_information');
            }

            if (empty($errors)) {

                if ($this->management_model->delete_black_list($black_list_term)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['add_sticker'])) {

            $this->require_role('role_action_manage_stickers');

            $sticker_name = sanitise_input($_POST['sticker_name'], 'text', '1', '40');

            $sticker_image = sanitise_image($_FILES['sticker_image']);

            if (is_null($sticker_name)) {
                $errors[] = Language::get('sticker_information');
            }

            if (is_null($sticker_image)) {
                $errors[] = Language::get('image_information');
            }

            if (empty($errors)) {

                $sticker_image_name = uniqid() . '.webp';

                $sticker_image_directory = 'cloud/global/stickers/';

                $sticker_image_file_path = $sticker_image_directory . $sticker_image_name;

                file_put_contents($sticker_image_file_path, $sticker_image['avatar.webp']);

                $sticker_location = '/' . $sticker_image_file_path;

                if ($this->management_model->add_sticker($sticker_name, $sticker_location)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['update_stickers'])) {

            $this->require_role('role_action_manage_stickers');

            if (isset($_POST['stickers']) && is_array($_POST['stickers'])) {

                foreach ($_POST['stickers'] as $sticker) {

                    $sticker_id = sanitise_input($sticker['sticker_id'], 'int');
                    $sticker_name = sanitise_input($sticker['sticker_name'], 'text', '1', '40');

                    if (is_null($sticker_id) || is_null($sticker_name)) {
                        $errors[] = Language::get('sticker_information');
                    }

                    if (empty($errors)) {

                        $sticker = $this->base_model->get_sticker_by_sticker_id($sticker_id);

                        if (!$sticker) {
                            Helpers::url_redirect(url: "/nope");
                        }

                        if ($this->management_model->update_sticker($sticker_id, $sticker_name)) {

                            Flash::set('successes', ['successes' => Language::get('update_success')]);

                            // Update message for each updated sticker ?

                        }
                    }
                }

                if (empty($errors)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['delete_sticker'])) {

            $this->require_role('role_action_manage_stickers');

            $sticker_id = sanitise_input($_POST['sticker_id'], 'int');

            if (is_null($sticker_id)) {
                $errors[] = Language::get('sticker_information');
            }

            if (empty($errors)) {

                $sticker = $this->base_model->get_sticker_by_sticker_id($sticker_id);

                if (!$sticker) {
                    Helpers::url_redirect(url: "/nope");
                }

                if (!empty($sticker['sticker_location']) && is_file($sticker['sticker_location'])) {
                    unlink($sticker['sticker_location']);
                }

                if ($this->management_model->delete_sticker($sticker['sticker_id'])) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['add_category'])) {

            $this->require_role('role_action_manage_categories');

            $category_name = sanitise_input($_POST['category_name'], 'text', '1', '40');

            $category_language = sanitise_input($_POST['category_language'], 'text', '2', '2');
            
            $category_priority = sanitise_input($_POST['category_priority'], 'int');


            if (is_null($category_name) || is_null($category_priority) || is_null($category_language)) {
                $errors[] = Language::get('category_information');
            }

            if (!$this->is_language($category_language)) {
                Helpers::url_redirect(url: "/nope");
            }

            if (empty($errors)) {

                if ($this->management_model->add_category($category_name, $category_priority)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['update_categories'])) {

            $this->require_role('role_action_manage_categories');

            if (isset($_POST['categories']) && is_array($_POST['categories'])) {

                foreach ($_POST['categories'] as $category) {

                    $category_id = sanitise_input($category['category_id'], 'int');
                    $category_name = sanitise_input($category['category_name'], 'text', '1', '40');
                    $category_language = sanitise_input($category['category_language'], 'text', '2', '2');
                    $category_priority = sanitise_input($category['category_priority'], 'int');

                    if (is_null($category_id) || is_null($category_name) || is_null($category_language) || is_null($category_priority)) {
                        $errors[] = Language::get('category_information');
                    }

                    if (!$this->is_language($category_language)) {
                        Helpers::url_redirect(url: "/nope");
                    }        

                    if (empty($errors)) {

                        $category = $this->model('ForumModel')->get_category($category_id);

                        if (!$category) {
                            Helpers::url_redirect(url: "/nope");
                        }

                        if ($this->management_model->update_category($category_id, $category_name, $category_language, $category_priority)) {

                            Flash::set('successes', ['successes' => Language::get('update_success')]);

                            // Update message for each updated category ?

                        }
                    }
                }

                if (empty($errors)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['delete_category'])) {

            $this->require_role('role_action_manage_categories');

            $category_id = sanitise_input($_POST['category_id'], 'int');

            if (is_null($category_id)) {
                $errors[] = Language::get('category_information');
            }

            $category = $this->model('ForumModel')->get_category($category_id);

            if (!$category) {
                Helpers::url_redirect(url: "/nope");
            }

            if (empty($errors)) {

                if ($this->management_model->delete_category($category['category_id'])) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }
            }
        }

        if (isset($_POST['add_role'])) {

            $this->require_role('role_action_manage_roles');

            $role_name = sanitise_input($_POST['role_name'], 'text', '1', '40');

            $role_color = sanitise_hex($_POST['role_color']);

            if (is_null($role_name)) {
                $errors[] = Language::get('role_name_information');
            }

            if (is_null($role_color)) {
                $errors[] = Language::get('role_color_information');
            }

            if (empty($errors)) {

                if ($this->management_model->add_role($role_name, $role_color)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }

            }

        }

        if (isset($_POST['update_role'])) {

            $this->require_role('role_action_manage_roles');

            $user_name = sanitise_input($_POST['user_name'], 'text', '3', '25');

            $user_role = sanitise_input($_POST['user_role'], 'int');

            if (is_null($user_name)) {
                $errors[] = Language::get('user_name_information');
            }

            if (is_null($user_role)) {
                Helpers::url_redirect(url: "/nope");
            }

            if (empty($errors)) {

                if ($this->management_model->update_role($user_name, $user_role)) {

                    Flash::set('successes', ['successes' => Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }

            }

        }

        if (isset($_POST['update_roles'])) {

            $this->require_role('role_action_manage_roles');

            if (!empty($_POST['roles']) && is_array($_POST['roles'])) {

                foreach ($_POST['roles'] as $role_id => $roleData) {

                    $role_id = (int) $role_id;

                    if ($role_id <= 0) {
                        continue;
                    }

                    $updated = [];

                    if (isset($roleData['role_name'])) {
                        $updated['role_name'] = sanitise_input($roleData['role_name'], 'text', '1', '40');
                    }
                    if (isset($roleData['role_color'])) {
                        $updated['role_color'] = sanitise_hex($roleData['role_color']);
                    }
                    if (isset($roleData['role_management'])) {
                        $updated['role_management'] = sanitise_input($roleData['role_management'], 'bool');
                    }
                    if (isset($roleData['role_action_ban'])) {
                        $updated['role_action_ban'] = sanitise_input($roleData['role_action_ban'], 'bool');
                    }
                    if (isset($roleData['role_action_kick'])) {
                        $updated['role_action_kick'] = sanitise_input($roleData['role_action_kick'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_user'])) {
                        $updated['role_action_delete_user'] = sanitise_input($roleData['role_action_delete_user'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_user_posts'])) {
                        $updated['role_action_delete_user_posts'] = sanitise_input($roleData['role_action_delete_user_posts'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_user_threads'])) {
                        $updated['role_action_delete_user_threads'] = sanitise_input($roleData['role_action_delete_user_threads'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_user_description'])) {
                        $updated['role_action_delete_user_description'] = sanitise_input($roleData['role_action_delete_user_description'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_user_avatar'])) {
                        $updated['role_action_delete_user_avatar'] = sanitise_input($roleData['role_action_delete_user_avatar'], 'bool');
                    }
                    if (isset($roleData['role_action_update_post'])) {
                        $updated['role_action_update_post'] = sanitise_input($roleData['role_action_update_post'], 'bool');
                    }
                    if (isset($roleData['role_action_hide_post'])) {
                        $updated['role_action_hide_post'] = sanitise_input($roleData['role_action_hide_post'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_post'])) {
                        $updated['role_action_delete_post'] = sanitise_input($roleData['role_action_delete_post'], 'bool');
                    }
                    if (isset($roleData['role_action_delete_thread'])) {
                        $updated['role_action_delete_thread'] = sanitise_input($roleData['role_action_delete_thread'], 'bool');
                    }
                    if (isset($roleData['role_action_lock_thread'])) {
                        $updated['role_action_lock_thread'] = sanitise_input($roleData['role_action_lock_thread'], 'bool');
                    }
                    if (isset($roleData['role_action_pin_thread'])) {
                        $updated['role_action_pin_thread'] = sanitise_input($roleData['role_action_pin_thread'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_site'])) {
                        $updated['role_action_manage_site'] = sanitise_input($roleData['role_action_manage_site'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_stickers'])) {
                        $updated['role_action_manage_stickers'] = sanitise_input($roleData['role_action_manage_stickers'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_users'])) {
                        $updated['role_action_manage_users'] = sanitise_input($roleData['role_action_manage_users'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_black_list'])) {
                        $updated['role_action_manage_black_list'] = sanitise_input($roleData['role_action_manage_black_list'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_categories'])) {
                        $updated['role_action_manage_categories'] = sanitise_input($roleData['role_action_manage_categories'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_chat_rooms'])) {
                        $updated['role_action_manage_chat_rooms'] = sanitise_input($roleData['role_action_manage_chat_rooms'], 'bool');
                    }
                    if (isset($roleData['role_action_manage_roles'])) {
                        $updated['role_action_manage_roles'] = sanitise_input($roleData['role_action_manage_roles'], 'bool');
                    }

                    if (
                        is_null($updated['role_name'])
                        || is_null($updated['role_color'])
                        || is_null($updated['role_management'])
                        || is_null($updated['role_action_ban'])
                        || is_null($updated['role_action_kick'])
                        || is_null($updated['role_action_delete_user'])
                        || is_null($updated['role_action_delete_user_posts'])
                        || is_null($updated['role_action_delete_user_threads'])
                        || is_null($updated['role_action_delete_user_description'])
                        || is_null($updated['role_action_delete_user_avatar'])
                        || is_null($updated['role_action_update_post'])
                        || is_null($updated['role_action_hide_post'])
                        || is_null($updated['role_action_delete_post'])
                        || is_null($updated['role_action_delete_thread'])
                        || is_null($updated['role_action_lock_thread'])
                        || is_null($updated['role_action_pin_thread'])
                        || is_null($updated['role_action_manage_site'])
                        || is_null($updated['role_action_manage_stickers'])
                        || is_null($updated['role_action_manage_users'])
                        || is_null($updated['role_action_manage_black_list'])
                        || is_null($updated['role_action_manage_categories'])
                        || is_null($updated['role_action_manage_chat_rooms'])
                        || is_null($updated['role_action_manage_roles'])
                    ) {
                        $errors[] = Language::get('incorrect_input');
                    }

                    if (empty($errors) && !empty($updated)) {

                        $this->management_model->update_roles($role_id, $updated);

                    }
                }
                if (empty($errors)) {

                    Flash::set('successes', [Language::get('update_success')]);

                    Helpers::url_redirect("/management");

                }
            }
        }


        if (isset($_POST['delete_role'])) {

            $this->require_role('role_action_manage_roles');

            $role_id = sanitise_input($_POST['delete_role'], 'int');

            if (is_null($role_id)) {
                $errors[] = Language::get('role_id_information');
            }

            if (empty($errors)) {

                if ($this->management_model->delete_role($role_id)) {

                    Flash::set('successes', [Language::get('update_success')]);

                    Helpers::url_redirect(url: "/management");

                }


            }
        }

        if (isset($_POST['flush_chat_rooms'])) {

            $this->require_role('role_action_manage_chat_room');

            $redis = RedisClient::get_instance();

            if ($redis->flushDB()) {

                Flash::set('successes', ['successes' => Language::get('update_success')]);

                Helpers::url_redirect(url: "/management");

            }

        }

        Flash::set('errors', ['errors' => $errors]);

        Helpers::url_redirect(url: "/management");

    }

}
