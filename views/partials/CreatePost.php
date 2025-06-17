<?php // views/partials/CreatePost.php ?>

<h4><?= Language::get('create_post'); ?></h4>

<?php if ($data['user_authenticated']): ?>

    <?php if (isset($data['cache']['CreatePost']['preview'])): ?>
        <details open>
            <hr>
            <summary><?= Language::get('preview'); ?></summary>
            <li class="post-container" style="position: relative; padding: 10px; margin-bottom: 10px;">
                <p><?= $data['cache']['CreatePost']['preview']['post_content_html']; ?></p>
            </li>
            <hr>
        </details>
    <?php endif; ?>

    <form method="POST" class="full-width">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <input type="hidden" name="page" value="<?= sanitise_output($data['public_params']['page']); ?>">

        <?php if (isset($data['cache']['CreatePost']['action_data']['action']) && $data['cache']['CreatePost']['action_data']['action'] == 'update_post'): ?>

            <input type="hidden" name="action"
                value="<?= sanitise_output($data['cache']['CreatePost']['action_data']['action']); ?>">
            <input type="hidden" name="post_id"
                value="<?= sanitise_output($data['cache']['CreatePost']['action_data']['post_id']); ?>">

            <p><em><?= Language::get('post_update'); ?></em></p>

        <?php endif; ?>

        <?php require 'views/partials/Stickers.php'; ?>

        <p>
            <textarea name="post_content" placeholder="<?= Language::get('post_content_help_text'); ?>" minlength="4"
                maxlength="40000"
                required><?= isset($data['cache']['CreatePost']['post_content']) ? sanitise_output($data['cache']['CreatePost']['post_content']) : ''; ?></textarea>

            <span class="helptext"><?= Language::get('update_time_help_text'); ?></span>
        </p>

        <button type="submit" name="show_preview"><?= Language::get('preview'); ?></button>

        <?php if ($_SESSION['user_posts'] >= 15): ?>
            <button type="submit" name="create_post">
                <?= isset($data['cache']['CreatePost']['action_data']['action']) && $data['cache']['CreatePost']['action_data']['action'] == 'update_post' ? Language::get('update') : Language::get('send'); ?>
                &raquo;
            </button>
        <?php else: ?>
            <?= $data['captcha_html']; ?>
        <?php endif; ?>

    </form>

<?php else: ?>

    <hr>

    <h3>
        <a href="/signin"><?= Language::get('sign_in'); ?></a>
    </h3>

<?php endif; ?>