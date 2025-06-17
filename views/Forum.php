<?php // views/Forum.php ?>

<!-- Categories Details -->

<?php if (!empty($data['private_params']['categories'])): ?>

    <h2><?= Language::get('categories'); ?></h2>

    <ul>
        <?php foreach ($data['private_params']['categories'] as $category): ?>
            <li>
                <a href="/forum/<?= sanitise_output($category['category_id']); ?>"><?= sanitise_output($category['category_name']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

<!-- Category Details -->

<?php elseif (!empty($data['private_params']['threads']) || !empty($data['private_params']['category'])): ?>

    <!-- Search Bar -->

     <form action="/forum/<?= sanitise_output($data['private_params']['category']['category_id'])?>" method="GET">

        <input type="text"
               name="search" 
               value="<?= isset($_GET['search']) ? sanitise_output($_GET['search']) : ''; ?>" 
               placeholder="<?= Language::get('search'); ?>..."
               minlength="1" 
               maxlength="70" 
               required
               style="width: 65%; height: 15%, box-sizing: border-box;">

        <button type="submit"><?= Language::get('search'); ?></button>

    </form>

    <h2>
        <?php if (!empty($data['private_params']['category'])): ?>
            <?= sanitise_output($data['private_params']['category']['category_name']); ?> :
        <?php else: ?>
            <?= sanitise_output($data['private_params']['category']['category_name']); ?> :
            <?= Language::get('search_results'); ?> :
        <?php endif; ?>
    </h2>

    <h3><a href="<?= sanitise_output($_SERVER['REQUEST_URI']); ?>" class="btn"><?= Language::get('refresh'); ?></a></h3>

    <?php if (!empty($data['private_params']['threads'])): ?>

        <ul class="discover-posts">

        <?php require 'views/partials/Pagination.php'; ?>

        <hr>

            <?php foreach ($data['private_params']['threads'] as $thread): ?>

                <li>

                <span><?php if ($thread['thread_pin']): ?> 📌 <?php endif; ?><?php if ($thread['thread_lock']): ?> 🔒 <?php endif; ?></span>

                    <div style="overflow-wrap: anywhere;">

                        <a href="/thread/<?= sanitise_output($thread['thread_id']); ?>">
                            <?= sanitise_output($thread['thread_title']); ?>
                        </a>

                        <small>

                            <span><?= sanitise_output($thread['post_count']); ?></span>

                            <br>

                            <small>
                                <?= Language::get('author'); ?> : 
                                <a href="/profile/<?= sanitise_output($thread['thread_author']); ?>">
                                    <?= sanitise_output($thread['thread_author']); ?>
                                </a>
                                |
                                <?= Language::get('last_reply'); ?> :
                                <a href="/profile/<?= sanitise_output($thread['thread_last_post_author']); ?>">
                                    <?= sanitise_output($thread['thread_last_post_author']); ?>
                                </a>
                                (<?= date('d/m/Y - H:i', $thread['thread_last_post_timestamp']); ?>).
                            </small>
                            
                        </small>

                    </div>
                </li>
            <?php endforeach; ?>

        <hr>

        <?php require 'views/partials/Pagination.php'; ?>

        </ul>

        <?php require 'views/partials/CreateThread.php'; ?>

        <?php else: ?>

        <p><?= Language::get('no_thread'); ?></p>

        <?php require 'views/partials/CreateThread.php'; ?>

    <?php endif; ?>

<?php else: ?>

    <p><?= Language::get('no_category_or_thread'); ?></p>

<?php endif; ?>