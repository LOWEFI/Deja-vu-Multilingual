<?php // views/Profile.php ?>

<h2><?= Language::get('profile'); ?></h2>

<?php if ($data['role_authorization']['role_action_ban']): ?>

    <?php if ($data['user']['user_ban']): ?>
        <a
            href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=unban&csrf_token=<?= urlencode($data['csrf_token']); ?>">
            <?= Language::get('unban'); ?>
        </a>
    <?php else: ?>
        <a
            href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=ban&csrf_token=<?= urlencode($data['csrf_token']); ?>">
            <?= Language::get('ban'); ?>
        </a>
    <?php endif; ?>
    |
<?php endif; ?>
<?php if ($data['role_authorization']['role_action_delete_user']): ?>
    <a
        href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=delete_user&csrf_token=<?= urlencode($data['csrf_token']); ?>">
        <?= Language::get('delete_user'); ?>
    </a>
    |
<?php endif; ?>
<?php if ($data['role_authorization']['role_action_delete_user_posts']): ?>
    <a
        href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=delete_user_posts&csrf_token=<?= urlencode($data['csrf_token']); ?>">
        <?= Language::get('delete_user_posts'); ?>
    </a>
    |
<?php endif; ?>
<?php if ($data['role_authorization']['role_action_delete_user_threads']): ?>
    <a
        href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=delete_user_threads&csrf_token=<?= urlencode($data['csrf_token']); ?>">
        <?= Language::get('delete_user_threads'); ?>
    </a>
    |
<?php endif; ?>
<?php if ($data['role_authorization']['role_action_delete_user_description']): ?>
    <a
        href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=delete_user_description&csrf_token=<?= urlencode($data['csrf_token']); ?>">
        <?= Language::get('delete_user_description'); ?>
    </a>
    |
<?php endif; ?>
<?php if ($data['role_authorization']['role_action_delete_user_avatar']): ?>
    <a
        href="/profile/<?= sanitise_output($data['user']['user_name']); ?>?action=delete_user_avatar&csrf_token=<?= urlencode($data['csrf_token']); ?>">
        <?= Language::get('delete_user_avatar'); ?>
    </a>
<?php endif; ?>

<h2>
    <?= sanitise_output($data['user']['user_name']) ?>
    <?php if (isset($data['user']['user_ban']) && $data['user']['user_ban'] === 1): ?>
        <small style="color: red;"> (<?= Language::get('user_banned'); ?>) </small>
    <?php endif; ?>

    <?php if ($data['user']['role_name'] ?? false): ?>
        <div style="color: <?= sanitise_output($data['user']['role_color']); ?>;">
            <?= sanitise_output($data['user']['role_name']); ?>.
        </div>
    <?php endif; ?>
</h2>

<div class="profile-container">
    <img src="<?= sanitise_output('/cloud/users/' . $data['user']['user_id'] . '/avatar/avatar.webp'); ?>" alt="avatar"
        class="profile-avatar">
    <div class="profile-text">
        <p><?= Language::get('first_seen'); ?> <?= date('d/m/Y - H:i', $data['user']['user_creation_timestamp']); ?></p>
        <p><?= Language::get('last_seen'); ?> <?= date('d/m/Y - H:i', $data['user']['user_action_timestamp']); ?></p>
        <p><?= Language::get('total_posts'); ?> <?= sanitise_output($data['total_posts']) ?></p>
    </div>
</div>

<hr>

<?php if (!empty($data['user_posts'])): ?>
    <div style="max-height:40vh; overflow-y:auto;">
        <ul>
            <?php foreach ($data['user_posts'] as $post): ?>
                <li class="post-container">
                    <p>
                        ✍️
                        | <?= date('d/m/Y - H:i', $post['post_timestamp']); ?>
                        | <?= sanitise_output($post['post_author']); ?> <?= Language::get('wrote_something'); ?>
                        | <a href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>#<?= sanitise_output($post['post_id']); ?>">
                            <?= Language::get('see'); ?>
                        </a>
                        (<?= Language::get('page'); ?>         <?= sanitise_output($post['post_page']); ?>)
                        <details><?= $post['post_content_html']; ?></details>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p><?= Language::get('no_post'); ?></p>
<?php endif; ?>

<hr>

<div>
    <h2><?= Language::get('user_description'); ?> : </h2>
    <p><?= $data['user']['user_description_html'] ?></p>
</div>