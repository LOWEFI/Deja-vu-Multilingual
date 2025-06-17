<?php // views/partials/CreateThread.php ?>

<h2><?= Language::get('create_thread'); ?></h2>

<?php if ($data['user_authenticated']): ?>

    <?php if (isset($data['cache']['CreateThread']['preview'])): ?>
        <details open>
            <hr>
            <summary><?= Language::get('preview'); ?></summary>

            <li class="post-container" style="position: relative; padding: 10px; margin-bottom: 10px;">
                <h3><?= sanitise_output($data['cache']['CreateThread']['preview']['thread_title']); ?></h3>
                <div><?= $data['cache']['CreateThread']['preview']['post_content_html']; ?></div>
            </li>

            <hr>
        </details>
    <?php endif; ?>

    <form method="POST" class="full-width">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <?php require 'views/partials/Stickers.php'; ?>

        <div style="margin-bottom: 10px;"><input type="text" name="thread_title"
                value="<?= isset($data['cache']['CreateThread']['thread_title']) ? sanitise_output($data['cache']['CreateThread']['thread_title']) : '' ?>"
                placeholder="<?= Language::get('thread_title'); ?>" minlength="4" maxlength="70" required>
        </div>

        <div style="margin-bottom: 10px;">
            <textarea name="post_content" placeholder="<?= Language::get('post_content_help_text'); ?>" minlength="4"
                maxlength="40000"
                required><?= isset($data['cache']['CreateThread']['post_content']) ? sanitise_output($data['cache']['CreateThread']['post_content']) : '' ?></textarea>

            <span class="helptext"><?= Language::get('update_time_help_text'); ?></span>
        </div>

        <button type="submit" name="show_preview"><?= Language::get('preview'); ?> &raquo;</button>
        
        <?php if ($_SESSION['user_posts'] >= 15): ?>
            <button type="submit" name="create_thread"><?= Language::get('create'); ?> &raquo;</button>
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