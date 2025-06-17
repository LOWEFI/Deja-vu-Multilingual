<?php // views/Management.php ?>

<h2><?= Language::get('management'); ?></h2>


<?php if ($data['role_authorization']['role_action_manage_site']): ?>

    <!-- Manage Site Settings Section -->
    <hr>

    <h2><?= Language::get('manage_site'); ?></h2>

    <form action="/management" method="POST" class="full-width">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <div>
            <small><?= Language::get('site_name'); ?> :</small>
            <input type="text" name="site_name" value="<?= sanitise_output($data['site_data']['site_name']); ?>"
                minlength="1" maxlength="35" required>
        </div>

        <div>
            <small><?= Language::get('site_description'); ?> :</small>
            <textarea name="site_description" minlength="1" maxlength="255"
                required><?= sanitise_output($data['site_data']['site_description']); ?></textarea>
        </div>

        <div>
            <small><?= Language::get('site_keywords'); ?> :</small>
            <input type="text" name="site_keywords" value="<?= sanitise_output($data['site_data']['site_keywords']); ?>"
                minlength="1" maxlength="510" required>
        </div>

        <div>
            <small><?= Language::get('site_information'); ?> :</small>
            <textarea name="site_information" minlength="1" maxlength="255"
                required><?= sanitise_output($data['site_data']['site_information']); ?></textarea>
        </div>

        <hr>

        <div>
            <small><?= Language::get('site_registration'); ?> :</small>
            <select name="site_registration">
                <option value="1" <?= $data['site_data']['site_registration'] == 1 ? 'selected' : ''; ?>>✅</option>
                <option value="0" <?= $data['site_data']['site_registration'] == 0 ? 'selected' : ''; ?>>❌</option>
            </select>
        </div>

        <hr>

        <div>
            <small><?= Language::get('site_chat'); ?> :</small>
            <select name="site_chat">
                <option value="1" <?= $data['site_data']['site_chat'] == 1 ? 'selected' : ''; ?>>✅</option>
                <option value="0" <?= $data['site_data']['site_chat'] == 0 ? 'selected' : ''; ?>>❌</option>
            </select>
        </div>

        <hr>

        <div>
            <small><?= Language::get('site_threads_per_page'); ?> :</small>
            <input type="number" name="site_threads_per_page"
                value="<?= sanitise_output($data['site_data']['site_threads_per_page']); ?>" minlength="1" maxlength="35"
                required>
        </div>

        <div>
            <small><?= Language::get('site_posts_per_page'); ?> :</small>
            <input type="number" name="site_posts_per_page"
                value="<?= sanitise_output($data['site_data']['site_posts_per_page']); ?>" minlength="1" maxlength="35"
                required>
        </div>

        <div>
            <small><?= Language::get('site_posts_per_profile'); ?> :</small>
            <input type="number" name="site_posts_per_profile"
                value="<?= sanitise_output($data['site_data']['site_posts_per_profile']); ?>" minlength="1" maxlength="35"
                required>
        </div>

        <button type="submit" name="update_site" style="margin-top: 10px;"><?= Language::get('update'); ?></button>

    </form>

<?php endif; ?>

<?php if ($data['role_authorization']['role_action_manage_stickers']): ?>

    <!-- Manager Stickers -->
    <hr>

    <h3><?= Language::get('manage_stickers'); ?></h3>

    <form action="/management" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <input type="text" name="sticker_name" placeholder="<?= Language::get('sticker_name'); ?>" minlength="1"
            maxlength="40" required>

        <input type="file" name="sticker_image" accept="image/*" required>

        <button type="submit" name="add_sticker"><?= Language::get('add'); ?></button>
    </form>

    <br>

    <form action="/management" method="POST">
        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <div class="listing-container">

            <table>
                <thead>
                    <tr>
                        <th><?= Language::get('sticker'); ?></th>
                        <th><?= Language::get('name'); ?></th>
                        <th><?= Language::get('location'); ?></th>
                        <th><?= Language::get('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['stickers_data'] as $sticker): ?>
                        <tr style="margin-bottom: 0;">
                            <td style="width:5%;">
                                <input type="hidden"
                                    name="stickers[<?= sanitise_output($sticker['sticker_id']); ?>][sticker_id]"
                                    value="<?= sanitise_output($sticker['sticker_id']); ?>">
                                <img src="<?= sanitise_output($sticker['sticker_location']); ?>"
                                    alt="<?= sanitise_output($sticker['sticker_name']); ?>" style="width:30px;">
                            </td>
                            <td style="width:25%;">
                                <input type="text"
                                    name="stickers[<?= sanitise_output($sticker['sticker_id']); ?>][sticker_name]"
                                    value="<?= sanitise_output($sticker['sticker_name']); ?>" minlength="1"
                                    maxlength="40" required style="width:90%;">
                            </td>
                            <td style="width:25%;">
                                <input type="text"
                                    name="stickers[<?= sanitise_output($sticker['sticker_id']); ?>][sticker_location]"
                                    value="<?= sanitise_output($sticker['sticker_location']); ?>" required style="width:90%;" disabled>
                            </td>
                            <td style="width:25%;">
                                <form action="/management" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">
                                    <input type="hidden" name="sticker_id"
                                        value="<?= sanitise_output($sticker['sticker_id']); ?>">
                                    <button type="submit" name="delete_sticker"><?= Language::get('delete'); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

        <button type="submit" name="update_stickers"><?= Language::get('update'); ?></button>

    </form>

<?php endif; ?>

<?php if ($data['role_authorization']['role_action_manage_black_list']): ?>

    <!-- Manage Black_list Section -->
    <hr>

    <h3><?= Language::get('manage_black_list'); ?></h3>

    <small><?= Language::get('black_list_term'); ?> :</small>
    <form action="/management" method="POST">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <input type="text" name="black_list_term" minlength="1" maxlength="255" required>

        <button type="submit" name="add_black_list"><?= Language::get('add'); ?></button>
    </form>

    <small><?= Language::get('black_list_term'); ?> :</small>
    <form action="/management" method="POST">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <select name="black_list_term" id="select_black_list_term" required>
            <option value=""><?= Language::get('black_list_term'); ?></option>
            <?php foreach ($data['black_list'] as $term): ?>
                <option value="<?= sanitise_output($term['black_list_term']); ?>">
                    <?= sanitise_output($term['black_list_term']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="delete_black_list"><?= Language::get('delete'); ?></button>

    </form>

<?php endif; ?>

<?php if ($data['role_authorization']['role_action_manage_categories']): ?>

    <!-- Manage Categories Section -->
    <hr>

    <h3><?= Language::get('manage_categories'); ?></h3>

    <form action="/management" method="POST">
        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <input type="text" name="category_name" placeholder="<?= Language::get('category'); ?>" minlength="1" maxlength="40"
            required style="width: 30%;">

        <input type="number" name="category_priority" placeholder="<?= Language::get('priority'); ?>" min="0" required
            style="width: 15%;">

        <select name="category_language">
            <?php foreach ($data['languages'] as $code => $label): ?>
                <option value="<?= sanitise_output($code) ?>">
                    <?= sanitise_output($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="add_category">
            <?= Language::get('update'); ?>
        </button>
    </form>

    <br>

    <form action="/management" method="POST">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <div class="listing-container">
            <table>
                <thead>
                    <tr style="margin-bottom: 0;">
                        <th><?= Language::get('category'); ?></th>
                        <th><?= Language::get('priority'); ?></th>
                        <th><?= Language::get('language'); ?></th>
                        <th><?= Language::get('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['categories'] as $category): ?>
                        <tr style="margin-bottom: 0;">
                            <td style="width: 30%;">
                                <input type="hidden"
                                    name="categories[<?= sanitise_output($category['category_id']); ?>][category_id]"
                                    value="<?= sanitise_output($category['category_id']); ?>">

                                <input type="text"
                                    name="categories[<?= sanitise_output($category['category_id']); ?>][category_name]"
                                    value="<?= sanitise_output($category['category_name']); ?>" minlength="1"
                                    maxlength="40" required style="width: 90%;">
                            </td>
                            <td style="width: 10%;">
                                <select name="categories[<?= sanitise_output($category['category_id']); ?>][category_language]">
                                    <?php foreach ($data['languages'] as $code => $label): ?>
                                        <option value="<?= sanitise_output($code) ?>"
                                                <?= $code === $category['category_language'] ? 'selected' : '' ?>>
                                            <?= sanitise_output($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="width: 20%;">
                                <input type="number"
                                    name="categories[<?= sanitise_output($category['category_id']); ?>][category_priority]"
                                    value="<?= sanitise_output($category['category_priority']); ?>" min="0" required
                                    style="width: 90%;">
                            </td>
                            <td style="width: 20%;">
                                <form action="/management" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">
                                    <input type="hidden" name="category_id"
                                        value="<?= sanitise_output($category['category_id']); ?>">
                                    <button type="submit" name="delete_category">
                                        <?= Language::get('delete'); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <button type="submit" name="update_categories">
            <?= Language::get('update'); ?>
        </button>
    </form>

<?php endif; ?>

<?php if ($data['role_authorization']['role_action_manage_roles']): ?>

    <hr>
    <!-- Manage Roles Section -->

    <h3><?= Language::get('manage_roles'); ?></h3>

    <strong><?= Language::get('users'); ?> (<?= sanitise_output($data['users_number']); ?>)</strong>

    <form action="/management" method="POST">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <select name="user_name" required>
            <option value=""><?= Language::get('user_name'); ?></option>
            <?php foreach ($data['users'] as $user): ?>
                <option value="<?= sanitise_output($user['user_name']); ?>">
                    <?= sanitise_output($user['user_name']); ?> -
                    <?= date("d/m/Y - H:i", $user['user_creation_timestamp']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="user_role" required>
            <option value=""><?= Language::get('user_role'); ?></option>
            <?php foreach ($data['roles'] as $role): ?>
                <option value="<?= sanitise_output($role['role_id']); ?>">
                    <?= sanitise_output($role['role_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="update_role"><?= Language::get('update'); ?> &raquo;</button>

    </form>

    <br>

    <form action="/management" method="POST">
        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <input type="text" name="role_name" placeholder="<?= Language::get('role_name'); ?>" minlength="1" maxlength="40"
            required style="width: 30%;">

        <input type="text" name="role_color" placeholder="#ffffff" required style="width: 15%;">

        <button type="submit" name="add_role">
            <?= Language::get('update'); ?>
        </button>
    </form>

    <br>

    <form action="/management" method="POST">
        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">
        <div class="listing-container">
            <table>
                <thead>
                    <tr style="margin-bottom: 0;">
                        <th><?= Language::get('role_name'); ?></th>
                        <th><?= Language::get('role_color'); ?></th>
                        <th><?= Language::get('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['roles'] as $role): ?>
                        <tr style="margin-bottom:0;">
                            <td style="width: 40%;">
                                <input type="text" name="roles[<?= sanitise_output($role['role_id']); ?>][role_name]"
                                    value="<?= sanitise_output($role['role_name']); ?>" minlength="1"
                                    maxlength="40" required style="width: 90%;">
                            </td>
                            <td style="width: 20%;">
                                <input type="text" name="roles[<?= sanitise_output($role['role_id']); ?>][role_color]"
                                    value="<?= sanitise_output($role['role_color']); ?>" minlength="4"
                                    maxlength="7" required style="width: 90%;">
                            </td>
                            <td style="width: 30%;">
                                <button type="submit" name="delete_role" value="<?= sanitise_output($role['role_id']); ?>">
                                    <?= Language::get('delete'); ?>
                                </button>
                            </td>
                        </tr>
                        <tr style="margin-bottom: 0;">
                            <td colspan="3">
                                <details>
                                    <summary><small><?= Language::get('see_permissions'); ?></small></summary>
                                    <div style="margin-top: 8px;">

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_management]" value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_management]"
                                                value="1" <?= $role['role_management'] == 1 ? 'checked' : ''; ?>>
                                            Management
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_ban]" value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_ban]"
                                                value="1" <?= $role['role_action_ban'] == 1 ? 'checked' : ''; ?>>
                                            Ban
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_kick]" value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_kick]"
                                                value="1" <?= $role['role_action_kick'] == 1 ? 'checked' : ''; ?>>
                                            Kick
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user]"
                                                value="1" <?= $role['role_action_delete_user'] == 1 ? 'checked' : ''; ?>>
                                            Delete User
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_posts]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_posts]"
                                                value="1" <?= $role['role_action_delete_user_posts'] == 1 ? 'checked' : ''; ?>>
                                            Delete User Posts
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_threads]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_threads]"
                                                value="1" <?= $role['role_action_delete_user_threads'] == 1 ? 'checked' : ''; ?>>
                                            Delete User Threads
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_description]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_description]"
                                                value="1" <?= $role['role_action_delete_user_description'] == 1 ? 'checked' : ''; ?>>
                                            Delete User Description
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_avatar]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_user_avatar]"
                                                value="1" <?= $role['role_action_delete_user_avatar'] == 1 ? 'checked' : ''; ?>>
                                            Delete User Avatar
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_update_post]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_update_post]"
                                                value="1" <?= $role['role_action_update_post'] == 1 ? 'checked' : ''; ?>>
                                            Edit Post
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_hide_post]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_hide_post]"
                                                value="1" <?= $role['role_action_hide_post'] == 1 ? 'checked' : ''; ?>>
                                            Hide Post
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_post]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_post]"
                                                value="1" <?= $role['role_action_delete_post'] == 1 ? 'checked' : ''; ?>>
                                            Delete Post
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_thread]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_delete_thread]"
                                                value="1" <?= $role['role_action_delete_thread'] == 1 ? 'checked' : ''; ?>>
                                            Delete Thread
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_lock_thread]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_lock_thread]"
                                                value="1" <?= $role['role_action_lock_thread'] == 1 ? 'checked' : ''; ?>>
                                            Lock Thread
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_pin_thread]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_pin_thread]"
                                                value="1" <?= $role['role_action_pin_thread'] == 1 ? 'checked' : ''; ?>>
                                            Pin Thread
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_site]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_site]"
                                                value="1" <?= $role['role_action_manage_site'] == 1 ? 'checked' : ''; ?>>
                                            Manage Site
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_stickers]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_stickers]"
                                                value="1" <?= $role['role_action_manage_stickers'] == 1 ? 'checked' : ''; ?>>
                                            Manage Stickers
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_users]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_users]"
                                                value="1" <?= $role['role_action_manage_users'] == 1 ? 'checked' : ''; ?>>
                                            Manage Users
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_black_list]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_black_list]"
                                                value="1" <?= $role['role_action_manage_black_list'] == 1 ? 'checked' : ''; ?>>
                                            Manage Black_List
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_categories]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_categories]"
                                                value="1" <?= $role['role_action_manage_categories'] == 1 ? 'checked' : ''; ?>>
                                            Manage Categories
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_roles]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_roles]"
                                                value="1" <?= $role['role_action_manage_roles'] == 1 ? 'checked' : ''; ?>>
                                            Manage Roles
                                        </label>

                                        <input type="hidden"
                                            name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_chat_rooms]"
                                            value="0">
                                        <label style="margin-right: 16px;">
                                            <input type="checkbox"
                                                name="roles[<?= sanitise_output($role['role_id']); ?>][role_action_manage_chat_rooms]"
                                                value="1" <?= $role['role_action_manage_chat_rooms'] == 1 ? 'checked' : ''; ?>>
                                            Manage Chat Rooms
                                        </label>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" name="update_roles">
            <?= Language::get('update'); ?>
        </button>
    </form>

<?php endif; ?>
<?php if ($data['role_authorization']['role_action_manage_chat_rooms']): ?>

    <!-- Manage Chat rooms Section -->
    <hr>

    <h3><?= Language::get('manage_chat_rooms'); ?></h3>

    <form action="/management" method="POST">

        <input type="hidden" name="csrf_token" value="<?= sanitise_output($data['csrf_token']); ?>">

        <button type="submit" name="flush_chat_rooms"><?= Language::get('flush_chat_rooms'); ?></button>

    </form>

<?php endif; ?>