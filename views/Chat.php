<?php // views/Chat.php ?>

<style>
        html {
                overflow-x: hidden;
        }

        .header-bis>h2,
        h4 {
                display: none;
                !important
        }

        nav {
                margin-bottom: 1rem;
                !important
        }

        footer>* {
                display: none;
                !important
        }

        button {
                margin: 0;
                !important
        }

        .input-group {
                display: flex;
        }

        .input-group input {
                flex: 1;
                padding: 10px;
                border-right: none;
                border-radius: 4px 0 0 4px;
                font-size: 16px;
        }

        .input-group button {
                padding: 0px 20px;
                border: 1px #ccc;
                border-left: none;
                border-radius: 0 4px 4px 0;
                background-color: #1b3a43;
                color: white;
                cursor: pointer;
        }

        .input-group button:hover {
                background-color: #FFFFFF26;
        }
</style>

<?php if (!empty($data['public_params']['chat_rooms']) && count($data['public_params']['chat_rooms']) > 1): ?>

        <details>
                <summary><?= Language::get('list_of_chat_rooms'); ?></summary>
                <ul class="chat-rooms-list">
                        <?php foreach ($data['public_params']['chat_rooms'] as $chat_room): ?>
                                <li>
                                        <a href="/chat/<?= sanitise_output($chat_room); ?>">
                                                <?= sanitise_output($chat_room); ?>
                                        </a>
                                </li>
                        <?php endforeach; ?>
                </ul>
        </details>

        <h6>
                <?= Language::get('active_chat_room'); ?> : <?= sanitise_output($data['public_params']['chat_room_id']); ?> 🟢
        </h6>

<?php endif; ?>

<?php if ($data['public_params']['chat_room_id']): ?>

        <iframe style="min-height:29rem; width:90vw; position: relative; left: 50%; transform: translateX(-50%); border:none;"
                src="/chat/call/<?= sanitise_output($data['public_params']['chat_room_id']); ?>?<?= uniqid(); ?>#bottom">
        </iframe>

        <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">
                <div class="input-group">
                        <input name="user_message" placeholder="<?= Language::get('post_content'); ?>" type="text" autofocus
                                minlength="1" maxlength="512" required>
                        <button name="lets_talk" type="submit"><?= Language::get('send'); ?></button>
                </div>
        </form>

        <div style="margin-bottom: 0.5rem;"></div>

                <?php if ($data['user_authenticated']): ?>
                        <?php if (isset($_SESSION['pause_chat_room'])): ?>
                                <a href="/chat/<?= sanitise_output($data['public_params']['chat_room_id']); ?>?action=unpause_chat_room&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                        <?= Language::get('unpause_chat_room') ?>
                                </a>
                        <?php else: ?>
                                <a href="/chat/<?= sanitise_output($data['public_params']['chat_room_id']); ?>?action=pause_chat_room&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                        <?= Language::get('pause_chat_room') ?>
                                </a>
                        <?php endif; ?>
                <?php endif; ?>

                <?php if ($data['role_authorization']['role_action_manage_chat_rooms']): ?>
                        <a href="/chat/<?= sanitise_output($data['public_params']['chat_room_id']); ?>?action=delete_chat_room&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                <?= Language::get('delete_chat_room') ?>
                        </a>
                        <a href="/chat/<?= sanitise_output($data['public_params']['chat_room_id']); ?>?action=flush_chat_room&csrf_token=<?= urlencode($data['csrf_token']); ?>">
                                <?= Language::get('flush_chat_room') ?>
                        </a>
                <?php endif; ?>

        </div>


<?php endif; ?>