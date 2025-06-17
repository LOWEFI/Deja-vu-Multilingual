<?php // /views/Account.php ?>

<h2><?= Language::get('account'); ?></h2>

<!-- Account Details --> <hr>

<h3><?= Language::get('account_details'); ?> : <?= sanitise_output($data['user']['user_name']); ?></h3>

<hr>

<div>
    <h3>
        <?= Language::get('user_avatar'); ?> :
    </h3>
    <img src="<?= sanitise_output('/cloud/users/' . $data['user']['user_id'] . '/avatar/avatar.webp?w=' . time()); ?>"
        alt="avatar" style="max-width:150px; height:auto;">
</div>

<div>
    <h3>
        <?= Language::get('user_description'); ?> : 
    </h3>
    <p>
        <?= $data['user']['user_description_html']; ?>
    </p>
</div>

<!-- Language Update Section --> <hr>

<form action="/account" method="POST">
    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

    <?= Language::get('language'); ?> :
    <select name="user_language" required>
        <?php foreach ($data['languages'] as $code => $label): ?>
            <option value="<?= sanitise_output($code) ?>"
                    <?= $code === ($_SESSION['user_language'] ?? '') ? 'selected' : '' ?>>
                <?= sanitise_output($label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="update_account_language">
        <?= Language::get('update'); ?>
    </button>
</form>

<!-- Password Update Section --> <hr>

<form action="/account" method="POST">

    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

    <h3><?= Language::get('update_password'); ?></h3>

    <table>

        <thead>
            <tr>
                <th><?= Language::get('user_current_password'); ?></th>
                <th><?= Language::get('user_new_password'); ?></th>
                <th><?= Language::get('user_confirm_new_password'); ?></th>
                <th><?= Language::get('action'); ?></th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    <input type="password" 
                           name="user_password"
                           placeholder="<?= Language::get('user_current_password'); ?>" 
                           minlength="6" 
                           maxlength="65" required>
                </td>
                <td>
                    <input type="password" 
                           name="user_new_password"
                           placeholder="<?= Language::get('user_new_password'); ?>" 
                           minlength="6" 
                           maxlength="65" required>
                </td>
                <td>
                    <input type="password" 
                           name="user_confirm_new_password"
                           placeholder="<?= Language::get('user_confirm_new_password'); ?>" 
                           minlength="6" maxlength="65" required>
                </td>
                <td>
                    <button type="submit" name="update_account_password">
                        <?= Language::get('update'); ?>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<!-- Description Update Section --> <hr>

<form action="/account" method="POST" class="full-width">

    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

    <h3><?= Language::get('update_description'); ?></h3>

    <div>
        <textarea name="user_description" rows="4" minlength="1" maxlength="5000"
            required><?= sanitise_output($data['user']['user_description']); ?></textarea>
        <span class="helptext">
            <?= Language::get('description_information'); ?>
        </span>
    </div>

    <button type="submit" name="update_account_description"><?= Language::get('update'); ?></button>

</form>

<!-- Avatar Update Section --> <hr>

<form action="/account" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

    <h3><?= Language::get('update_avatar'); ?></h3>

    <div>
        <input type="file" name="user_avatar" accept="image/*" required>
        <span class="helptext">
            <?= Language::get('image_information'); ?>
        </span>
    </div>

    <button type="submit" name="update_account_avatar"><?= Language::get('update'); ?></button>

</form>