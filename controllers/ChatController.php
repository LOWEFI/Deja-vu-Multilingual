<?php

// controllers/ChatController.php

class ChatController extends BaseController
{

    private $management_model;

    public function __construct()
    {
        parent::__construct();
        $this->management_model = $this->model('ManagementModel');
    }

    public function index($chat_room_id = null)
    {
        $this->require_authentication();

        if ($GLOBALS['site_data']['site_chat'] === 0) {

            Flash::set('errors', ['errors' => Language::get('closed_chat_information')]);

            Helpers::url_redirect(url: "/home");

        } elseif (time() - $_SESSION['user_kick_timestamp'] <= 15 * 60) {

            Flash::set('errors', ['errors' => Language::get('kicked_information')]);

            Helpers::url_redirect(url: "/home");

        } elseif ($_SESSION['user_posts'] < 15) {

            Flash::set('errors', ['errors' => Language::get('level_information')]);

            Helpers::url_redirect(url: "/home");

        }

        $default_chat_rooms = ['1'];

        if (!is_null($chat_room_id)) {

            $chat_room_id = sanitise_input($chat_room_id, 'int');

            if (
                is_null($chat_room_id)
                || !in_array($chat_room_id, $default_chat_rooms)
            ) {
                Helpers::url_redirect("/nope");
            }

        }

        $public_params = [
            "chat_room_id" => $chat_room_id ?? null,
        ];

        $private_params = [
            "chat_room_id" => $chat_room_id ?? null,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (is_null($chat_room_id)) {

                $public_params['chat_room_id'] = 1;

            }

            $this->handle_chat($public_params, $private_params);

        } elseif (isset($_GET['action'])) {

            $action_data = [
                'action' => $_GET['action'] ?? null,
                'redis_id' => $_GET['redis_id'] ?? null,
                'user_name' => $_GET['user_name'] ?? null,
            ];

            $this->handle_action($public_params, $private_params, $action_data);

        } else {

            $redis = RedisClient::get_instance();

            foreach ($default_chat_rooms as $default_room) {
                if (!$redis->sismember('chat_rooms', $default_room)) {
                    $redis->sadd('chat_rooms', $default_room);
                }
            }

            $chat_rooms = $redis->smembers('chat_rooms');

            $public_params['chat_rooms'] = $chat_rooms;

            if (!is_null($chat_room_id)) {

                $chat_room_messages = $redis->lrange("chat_room:{$chat_room_id}:messages", 0, -1);

                $public_params['chat_room_messages'] = $chat_room_messages;

                $this->chat_render($public_params, $private_params);

            } else {

                if (count($chat_rooms) == 1) {

                    $chat_room_id = array_values($chat_rooms)[0];

                    $chat_room_messages = $redis->lrange("chat_room:{$chat_room_id}:messages", 0, -1);

                    $public_params['chat_room_id'] = $chat_room_id;

                    $public_params['chat_room_messages'] = $chat_room_messages;

                    $this->chat_render($public_params, $private_params);

                } else {

                    $this->chat_render($public_params, $private_params);

                }
            }

        }

    }

    public function call($chat_room_id = null)
    {

        $this->require_authentication();

        if ($GLOBALS['site_data']['site_chat'] === 0) {

            Flash::set('errors', ['errors' => Language::get('closed_chat_information')]);

            Helpers::url_redirect(url: "/home");

        } elseif (time() - $_SESSION['user_kick_timestamp'] <= 15 * 60) {

            Flash::set('errors', ['errors' => Language::get('kicked_information')]);

            Helpers::url_redirect(url: "/home");

        } elseif ($_SESSION['user_posts'] < 15) {

            Flash::set('errors', ['errors' => Language::get('level_information')]);

            Helpers::url_redirect(url: "/home");

        }

        $chat_room_id = sanitise_input($chat_room_id, 'int');

        if (is_null($chat_room_id)) {
            Helpers::url_redirect("/nope");
        }

        $redis = RedisClient::get_instance();

        $raw_messages = $redis->lrange("chat_room:{$chat_room_id}:messages", 0, -1);

        $chat_room_messages = [];

        foreach ($raw_messages as $raw) {

            $message_data = json_decode($raw, true);

            if (!$message_data) {
                continue;
            }
    
            $chat_room_messages[] = [
                'redis_id'          => $message_data['redis_id'],
                'user_name'         => $message_data['user_name'],
                'user_id'           => $message_data['user_id'],
                'message'           => Parsedown($message_data['message'], false, false, true, false),
                'message_timestamp' => $message_data['message_timestamp'],
                'is_mine'           => ($_SESSION['user_id'] == $message_data['user_id'])
            ];
        }
    
        $data = [
            'chat_room_id'       => $chat_room_id,
            'chat_room_messages' => $chat_room_messages,
        ];
    
        $this->render('partials/ChatRoom', $data, true);
        exit();

    }

    // Render

    private function chat_render($public_params, $private_params)
    {

        // Base

        $GLOBALS['site_data']['site_title'] .= " - " . Language::get('chat');

        $this->render('Chat', [
            'public_params' => $public_params,
            'private_params' => $private_params,
        ]);

        exit();
    }

    // Handle Action on Chat

    private function handle_action($public_params, $private_params, $action_data)
    {

        $this->require_authentication();

        $action = sanitise_input($action_data['action'], 'text', '1', '255');

        if (is_null($action)) {
            Helpers::url_redirect("/nope");
        }

        if (!is_null($action_data['redis_id'])) {
            $redis_id = sanitise_input($action_data['redis_id'], 'text', '1', '255');
            if (is_null($redis_id)) {
                Helpers::url_redirect("/nope");
            }

        }

        if (!is_null($action_data['user_name'])) {
            $user_name = sanitise_input($action_data['user_name'], 'text', '1', '25');
            if (is_null($user_name)) {
                Helpers::url_redirect("/nope");
            }

        }

        switch ($action) {
            case 'delete_message':
                $this->require_role('role_action_manage_chat_rooms');
                $redis = RedisClient::get_instance();
                $chat_room_messages = $redis->lrange("chat_room:{$public_params['chat_room_id']}:messages", 0, -1);
                $redis_id = trim($redis_id ?? '');
                if (empty($redis_id)) {
                    Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");
                }
                $target_index = null;
                foreach ($chat_room_messages as $index => $raw) {
                    $data = json_decode($raw, true);
                    if ($data && isset($data['redis_id']) && $data['redis_id'] === $redis_id) {
                        $target_index = $index;
                        break;
                    }
                }
                if ($target_index !== null) {
                    $redis->lset("chat_room:{$public_params['chat_room_id']}:messages", $target_index, "__TO_DELETE__");
                    $redis->lrem("chat_room:{$public_params['chat_room_id']}:messages", "__TO_DELETE__", 1);
                    Helpers::url_redirect("/chat/call/{$public_params['chat_room_id']}/#bottom");
                }
                break;
            case 'kick':
                $this->require_role('role_action_manage_chat_rooms');
                if (
                    $this->management_model->update_user_row(
                        ['user_kick_timestamp' => time()],
                        ['user_name' => $user_name]
                    )
                ) {
                    $redis = RedisClient::get_instance();
                    $message_data = [
                        'redis_id' => uniqid(),
                        'user_name' => 'System',
                        'user_id' => 0,
                        'message' => "$user_name"
                    ];
                    $redis->rpush("chat_room:{$public_params['chat_room_id']}:messages", json_encode($message_data));
                    Helpers::url_redirect("/chat/call/{$public_params['chat_room_id']}/#bottom");
                }
                break;
            case 'pause_chat_room':
                $this->require_authentication();
                $_SESSION['pause_chat_room'] = true;
                Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");
                break;
            case 'unpause_chat_room':
                $this->require_authentication();
                unset($_SESSION['pause_chat_room']);
                Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");
                break;
            case 'flush_chat_room':
                $this->require_role('role_action_manage_chat_rooms');
                $redis = RedisClient::get_instance();
                $redis->del("chat_room:{$public_params['chat_room_id']}:messages");
                if (!empty($keys)) {
                    $redis->del($keys);
                }
                Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");
                break;
            case 'delete_chat_room':
                $this->require_role('role_action_manage_chat_rooms');
                $redis = RedisClient::get_instance();
                $redis->del("chat_room:{$public_params['chat_room_id']}:messages");
                $redis->srem('chat_rooms', $public_params['chat_room_id']);
                Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");
                break;
            default:
                Helpers::url_redirect("/nope");
        }
    }

    // Handle
    
    private function handle_chat($public_params, $private_params)
    {
        $this->require_authentication();

        if ($GLOBALS['site_data']['site_chat'] === 0) {

            Flash::set('errors', ['errors' => Language::get('closed_chat_information')]);

            Helpers::url_redirect(url: "/home");

        } elseif (time() - $_SESSION['user_kick_timestamp'] <= 15 * 60) {

            Flash::set('errors', ['errors' => Language::get('kicked_information')]);

            Helpers::url_redirect(url: "/home");

        } elseif ($_SESSION['user_posts'] < 15) {

            Flash::set('errors', ['errors' => Language::get('level_information')]);

            Helpers::url_redirect(url: "/home");

        }

        $rate_limit = $this->is_rate_limited('chat', 15, 60);

        if (!is_null($rate_limit)) {

            Flash::set('errors', ['errors' => $rate_limit]);

            Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/#top");

        }


        if (isset($_POST['lets_talk'])) {

            $user_message = sanitise_input($_POST['user_message'], 'text', '1', '512');

            if (is_null($user_message)) {
                Helpers::url_redirect("/nope");
            }

            if ($this->is_black_listed($user_message)) {
                Helpers::url_redirect(url: "/nope");
            }

            $redis = RedisClient::get_instance();

            $message_data = [
                'redis_id' => uniqid(),
                'user_name' => $_SESSION['user_name'],
                'user_id' => $_SESSION['user_id'],
                'message' => $user_message,
                'message_timestamp' => time()
            ];

            $redis->rpush("chat_room:{$public_params['chat_room_id']}:messages", json_encode($message_data));

            $current_length = $redis->llen("chat_room:{$public_params['chat_room_id']}:messages");

            if ($current_length > 100) {
                $redis->ltrim("chat_room:{$public_params['chat_room_id']}:messages", $current_length - 100, -1);
            }

            Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");

        } else {

            Helpers::url_redirect("/chat/{$public_params['chat_room_id']}/");

        }
    }

}