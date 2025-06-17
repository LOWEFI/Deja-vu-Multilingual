<?php // views/Signup.php ?>

<h2><?= Language::get('sign_up'); ?></h2>

<form action="/signup" method="POST">

    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

    <div style="margin-right: 10px; margin-bottom: 10px;">
        <input type="text" name="user_name" placeholder="<?= Language::get('user_name'); ?>" minlength="3"
            maxlength="25" required>
    </div>

    <div style="margin-right: 10px; margin-bottom: 10px;">
        <?= Language::get('language'); ?> :
        <select name="user_language">
            <?php foreach ($data['languages'] as $code => $label): ?>
                <option value="<?= sanitise_output($code) ?>">
                    <?= sanitise_output($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-right: 10px; margin-bottom: 10px;">
        <input type="password" name="user_password" placeholder="<?= Language::get('user_password'); ?>" minlength="6"
            maxlength="65" required>
    </div>

    <div style="margin-right: 10px; margin-bottom: 10px;">
        <input type="password" name="user_confirm_password" placeholder="<?= Language::get('user_confirm_password'); ?>"
            minlength="6" maxlength="65" required>
    </div>

    <?= $data['captcha_html']; ?>

    <!-- <button type="submit" name="sign_up"><?= Language::get('sign_up'); ?> &raquo;</button> -->

</form>

<span>
    <?= Language::get('already_signed_up_question'); ?>
    <a href="/signin">
        <?= Language::get('sign_in'); ?>
    </a>
</span>