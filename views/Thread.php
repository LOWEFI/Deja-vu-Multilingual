<?php // views/Thread.php ?>

<!-- Thread Details -->

<div>
    <h2>
        <?= Language::get('thread_title'); ?> :

        <?php if ($data['private_params']['thread']['thread_pin']): ?> 📌 <?php endif; ?>
        <?php if ($data['private_params']['thread']['thread_lock']): ?> 🔒 <?php endif; ?>

        <?= sanitise_output($data['private_params']['thread']['thread_title']); ?>

    </h2>

    <small><a href="/forum/<?= sanitise_output($data['private_params']['thread']['thread_category']); ?>">←
            <?= Language::get('back_to_list'); ?></a></small>

</div>

<!-- Manage Thread  : Lock/Unlock - Pin/Unpin -->

<?php if ($data['role_authorization']['role_action_lock_thread'] || $data['role_authorization']['role_action_lock_thread'] || $data['role_authorization']['role_action_delete_thread']): ?>
    <div style="margin-top: 10px;">
        <?php if ($data['role_authorization']['role_action_delete_thread']): ?>
            <a
                href="<?= sanitise_output($data['url']); ?>&action=delete_thread&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('delete'); ?></a>
            |
        <?php endif; ?>
        <?php if ($data['role_authorization']['role_action_lock_thread']): ?>
            <?php if ($data['private_params']['thread']['thread_lock']): ?>
                <a
                    href="<?= sanitise_output($data['url']); ?>&action=unlock_thread&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('unlock'); ?></a>
            <?php else: ?>
                <a
                    href="<?= sanitise_output($data['url']); ?>&action=lock_thread&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('lock'); ?></a>
            <?php endif; ?>
            |
        <?php endif; ?>
        <?php if ($data['role_authorization']['role_action_pin_thread']): ?>
            <?php if ($data['private_params']['thread']['thread_pin']): ?>
                <a
                    href="<?= sanitise_output($data['url']); ?>&action=unpin_thread&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('unpin'); ?></a>
            <?php else: ?>
                <a
                    href="<?= sanitise_output($data['url']); ?>&action=pin_thread&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('pin'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Thread Author -->

<small>
    <?= Language::get('started_by'); ?> :
    <a href="/profile/<?= sanitise_output($data['private_params']['thread']['thread_author']); ?>"><?= sanitise_output($data['private_params']['thread']['thread_author']); ?></a>
</small>

<!-- Posts Details -->

<?php if (!empty($data['private_params']['posts'])): ?>

    <ul>

        <?php require 'views/partials/Pagination.php'; ?>

        <hr>

        <?php foreach ($data['private_params']['posts'] as $post): ?>

            <li class="post-container">

                <div id="<?= sanitise_output($post['post_id']) ?>"></div>

                <div class="post-profile-container">

                    <img src="<?= sanitise_output('/cloud/users/' . $post['user_id'] . '/avatar/avatar-min.webp'); ?>"
                        alt="avatar" style="width:45px; height:auto; border-radius:10%;">

                    <div class="post-profile">

                        <div class="top-info">
                            <strong>
                                <?php if ($post['user_ban'] !== 1): ?>
                                    <a  href="/profile/<?= sanitise_output($post['post_author']); ?>"><?= sanitise_output($post['post_author']); ?></a>
                                <?php else: ?>
                                    <s><a href="/profile/<?= sanitise_output($post['post_author']); ?>"><?= sanitise_output($post['post_author']); ?></a></s>
                                <?php endif; ?>
                            </strong>

                            <small style="font-size: 10px;">
                                (<?= date('d/m/Y - H:i', $post['post_timestamp']); ?>) :
                            </small>

                        </div>

                        <div class="bottom-info">

                            <?php if ($post['role_name'] ?? false): ?>
                                <small style="color: <?= sanitise_output($post['role_color']); ?>;">
                                    <?= sanitise_output($post['role_name']); ?>.</small>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>

                <p>
                    <?= $post['post_content_html']; ?>
                </p>

                <br>

                <div class="post-action-container">

                    <?php if ($data['user_authenticated']): ?>
                        <a
                            href="<?= sanitise_output($data['url']); ?>&action=quote_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#bottom"><?= Language::get('quote'); ?></a>
                        <?php if ($_SESSION['user_name'] === $post['post_author'] || $data['role_authorization']['role_action_update_post']): ?>
                            | <a
                                href="<?= sanitise_output($data['url']); ?>&action=update_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#bottom"><?= Language::get('update'); ?></a>
                        <?php endif; ?>
                        <?php if ($data['role_authorization']['role_action_hide_post']): ?>
                            |
                            <?php if ($post['post_hide'] === 1): ?>
                                <a
                                    href="<?= sanitise_output($data['url']); ?>&action=unhide_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('unhide'); ?></a>
                            <?php else: ?>
                                <a
                                    href="<?= sanitise_output($data['url']); ?>&action=hide_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('hide'); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($data['role_authorization']['role_action_delete_post']): ?>
                            |
                            <a
                                href="<?= sanitise_output($data['url']); ?>&action=delete_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#bottom"><?= Language::get('delete'); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                    |
                    <a
                        href="<?= sanitise_output($data['url']); ?>#<?= sanitise_output($post['post_id']); ?>"><?= Language::get('permalink'); ?></a>

                </div>

            </li>

        <?php endforeach; ?>

        <hr>

        <?php require 'views/partials/Pagination.php'; ?>

    </ul>

<?php else: ?>

    <p><?= Language::get('no_post'); ?></p>

<?php endif; ?>

<small><a href="/forum/<?= sanitise_output($data['private_params']['thread']['thread_category']); ?>">←
        <?= Language::get('back_to_list'); ?></a></small>

<?php if ($data['private_params']['thread']['thread_lock'] != 1 || $data['role_authorization']['role_action_lock_thread']): ?>

    <h2><?= Language::get('reply'); ?></h2>

    <?php require 'views/partials/CreatePost.php'; ?>

<?php else: ?>

    <p><?= Language::get('thread_locked'); ?></p>

<?php endif; ?>