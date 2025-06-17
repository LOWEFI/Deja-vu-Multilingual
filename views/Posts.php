<?php // views/Posts.php ?>

<div>
    <h2>
        <?= Language::get('posts_list'); ?> (<?= sanitise_output($data['private_params']['total_posts']) ?>) :
    </h2>

</div>

<!-- Posts Details -->

<?php if (!empty($data['private_params']['posts'])): ?>

    <ul>

        <?php require 'views/partials/Pagination.php'; ?>

        <hr>

        <?php foreach ($data['private_params']['posts'] as $post): ?>


            <a
                href="/thread/<?= sanitise_output($post['post_thread_id']); ?>"><?= sanitise_output($post['thread_title']); ?></a>

            <hr>

            <li class="post-container">

                <div id="<?= sanitise_output($post['post_id']) ?>"></div>

                <div class="post-profile-container">

                    <img src="<?= sanitise_output('/cloud/users/' . $post['user_id'] . '/avatar/avatar-min.webp'); ?>"
                        alt="avatar" style="width:45px; height:auto; border-radius:10%;">

                    <div class="post-profile">

                        <div class="top-info">
                            <strong>
                                <?php if ($post['user_ban'] !== 1): ?>
                                    <a
                                        href="/profile/<?= sanitise_output($post['post_author']); ?>"><?= sanitise_output($post['post_author']); ?></a>
                                <?php else: ?>
                                    <s><a
                                            href="/profile/<?= sanitise_output($post['post_author']); ?>"><?= sanitise_output($post['post_author']); ?></a></s>
                                <?php endif; ?>
                            </strong>

                            <small>
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
                            href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>&action=quote_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#bottom"><?= Language::get('quote'); ?></a>
                        <?php if ($_SESSION['user_name'] === $post['post_author'] || $data['role_authorization']['role_action_update_post']): ?>
                            | <a
                                href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>&action=update_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#bottom"><?= Language::get('update'); ?></a>
                        <?php endif; ?>
                        <?php if ($data['role_authorization']['role_action_hide_post']): ?>
                            |
                            <?php if ($post['post_hide'] === 1): ?>
                                <a
                                    href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>&action=unhide_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#<?= sanitise_output($post['post_id']); ?>"><?= Language::get('unhide'); ?></a>
                            <?php else: ?>
                                <a
                                    href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>&action=hide_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>#<?= sanitise_output($post['post_id']); ?>"><?= Language::get('hide'); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($data['role_authorization']['role_action_delete_post']): ?>
                            |
                            <a
                                href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>&action=delete_post&post_id=<?= sanitise_output($post['post_id']); ?>&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('delete'); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                    |
                    <a
                        href="/thread/<?= sanitise_output($post['post_thread_id']); ?>?page=<?= sanitise_output($post['post_page']); ?>#<?= sanitise_output($post['post_id']); ?>"><?= Language::get('permalink'); ?></a>

                </div>

            </li>

        <?php endforeach; ?>

        <hr>

        <?php require 'views/partials/Pagination.php'; ?>

    </ul>

<?php else: ?>

    <p><?= Language::get('no_post'); ?></p>

<?php endif; ?>