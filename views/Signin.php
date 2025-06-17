<?php // views/Signin.php ?>

<h2><?= Language::get('sign_in'); ?></h2>

<form action="/signin" method="POST">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <div style="margin-bottom: 10px;">
                <strong><?= Language::get('user_name'); ?></strong>
                <input type="text" 
                       name="user_name" 
                       placeholder="<?= Language::get('user_name'); ?>"
                       minlength="3" 
                       maxlength="25" required>
        </div>

        <div style="margin-bottom: 10px;">
                <strong><?= Language::get('user_password'); ?></strong>
                <input type="password" 
                       name="user_password" 
                       placeholder="<?= Language::get('user_password'); ?>"
                       minlength="6" 
                       maxlength="65" required>
        </div>

        <?= $data['captcha_html']; ?>

        <!-- <button type="submit" name="sign_in"><?= Language::get('sign_in'); ?> &raquo;</button> -->

</form>

<span>
        <?= Language::get('not_signed_up_question'); ?>
        <a href="/signup">
                <?= Language::get('sign_up'); ?>
        </a>
</span>