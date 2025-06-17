<?php if (!isset($_SESSION['pause_chat_room'])): ?>
    <meta http-equiv="refresh"
        content="5;url=<?= sanitise_output(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>?<?= uniqid(); ?>#bottom">
<?php endif; ?>

<style>
    .chat-room-container {
        height: 100%;
        /* width: 100%; */
        overflow-y: auto;
        padding: 7px;
        background-color: #1c1e23;
        border-left: 0.5px solid #00FF41;
        border-right: 0.5px solid #00FF41;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .chat-room-message-wrapper {
        max-width: 70%;
    }

    .chat-room-user-avatar {
        width: 30px;
        height: 30px;
        margin-right: 10px;
        border-radius: 4px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .my-chat-room-messages-group .chat-room-user-avatar {
        margin-right: 0;
        margin-left: 10px;
    }

    .chat-room-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .chat-room-messages-group {
        display: flex;
        align-items: flex-start;
        margin-bottom: 3px;
        width: 100%;
    }

    .chat-room-messages-group.my-chat-room-messages-group {
        justify-content: flex-end;
    }

    .chat-room-message-content {
        background-color: #0b0b0c;
        padding: 8px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: inline-block;
    }

    .meta-data {
        font-size: 10px;
        color: #dddddd;
        margin-top: 3px;
        display: block;
    }

    .meta-data a {
        color: #8cc2dd;
    }

    .first-line {
        display: flex;
    }

    .chat-room-user-name {
        font-weight: bold;
        color: #00FF41;
        font-size: 14px;
        text-decoration: none;
    }

    .chat-room-user-messages {
        font-size: 14px;
        color: #ddd;
        line-height: 1.1;
        word-wrap: break-word;
        word-break: break-all;
    }

    .chat-room-system-messages {
        justify-content: center;
        width: 100%;
        display: flex;
        align-items: flex-start;
        margin-top: 0;
        margin-bottom: 0;
    }

    .chat-room-system-messages .chat-room-message-content {
        background-color: transparent;
        color: rgb(196, 0, 0);
        text-align: center;
    }
</style>

<div class="chat-room-container">

    <?php if (!empty($data['chat_room_messages'])): ?>

        <?php foreach ($data['chat_room_messages'] as $message): ?>

            <?php if ($message['user_id'] == 0): ?>
                <div class="chat-room-messages-group chat-room-system-messages">
                    <div class="chat-room-message-content">
                        <?= Language::get('kicked') ?> : <?= sanitise_output($message['message']); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="chat-room-messages-group <?= ($message['is_mine'] ? 'my-chat-room-messages-group' : ''); ?>">
                    
                    <?php if (!$message['is_mine']): ?>
                        <div class="chat-room-user-avatar">
                            <img src="<?= sanitise_output('/cloud/users/' . $message['user_id'] . '/avatar/avatar-min.webp'); ?>"
                                alt="chat-room-user-avatar">
                        </div>
                    <?php endif; ?>

                    <div class="chat-room-message-wrapper">
                        <?php if ($message['is_mine']): ?>
                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                <div class="chat-room-message-content">
                                    <div class="first-line" style="justify-content: flex-end;">
                                        <span class="chat-room-user-messages"><?= sanitise_output($message['message']); ?></span>
                                        <a href="/profile/<?= sanitise_output($message['user_name']); ?>"
                                            target="_blank" class="chat-room-user-name" style="margin-left: 10px;">
                                            <?= sanitise_output($message['user_name']); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="meta-data">
                                    <?= date('d/m/Y - H:i', $message['message_timestamp']); ?>
                                    <?php if ($data['role_authorization']['role_action_manage_chat_rooms']): ?>
                                        <a href="/chat/<?= sanitise_output($data['chat_room_id']); ?>?action=delete_message&redis_id=<?= sanitise_output($message['redis_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                            <?= Language::get('delete') ?>
                                        </a>
                                        <a href="/chat/<?= sanitise_output($data['chat_room_id']); ?>?action=kick&user_name=<?= sanitise_output($message['user_name']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                            <?= Language::get('kick') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="chat-room-message-content">
                                <div class="first-line">
                                    <a href="/profile/<?= sanitise_output($message['user_name']); ?>" target="_blank"
                                        class="chat-room-user-name">
                                        <?= sanitise_output($message['user_name']); ?>
                                    </a>
                                    <span class="chat-room-user-messages" style="margin-left: 10px;">
                                        <?= sanitise_output($message['message']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="meta-data">
                                <?= date('d/m/Y - H:i', $message['message_timestamp']); ?>
                                <?php if ($data['role_authorization']['role_action_manage_chat_rooms']): ?>
                                    <a href="/chat/<?= sanitise_output($data['chat_room_id']); ?>?action=delete_message&redis_id=<?= sanitise_output($message['redis_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                        <?= Language::get('delete') ?>
                                    </a>
                                    <a href="/chat/<?= sanitise_output($data['chat_room_id']); ?>?action=kick&user_name=<?= sanitise_output($message['user_name']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                        <?= Language::get('kick') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($message['is_mine']): ?>
                        <div class="chat-room-user-avatar">
                            <img src="<?= sanitise_output('/cloud/users/' . $message['user_id'] . '/avatar/avatar-min.webp'); ?>"
                                alt="chat-room-user-avatar">
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        <?php endforeach; ?>

    <?php else: ?>

        <p style="text-align: center; color: #ffffff;"><?= Language::get('empty_chat_room') ?></p>

    <?php endif; ?>

    <a id="bottom"></a>

</div>