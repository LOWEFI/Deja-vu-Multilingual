<?php // views/partials/Pagination.php ?>

<div class="pagination">

    <?php $page = isset($data['public_params']['page']) ? (int) $data['public_params']['page'] : 1;
          $total_pages = isset($data['private_params']['total_pages']) ? (int) $data['private_params']['total_pages'] : 1;

          $page = max(1, min($page, $total_pages)); ?>

    <?php if ($page > 1): ?>
        <a href="<?= sanitise_output(build_pagination_url($page - 1)) ?>" class="prev">&laquo;
            <?= Language::get('previous'); ?></a>
    <?php else: ?>
        <span class="prev disabled">&laquo; <?= Language::get('previous'); ?></span>
    <?php endif; ?>

    <span class="page-numbers"><?= str_pad(sanitise_output($page), 3, ' ', STR_PAD_LEFT) ?> /
        <?= str_pad(sanitise_output($total_pages), 3, ' ', STR_PAD_LEFT) ?></span>

    <?php if ($page < $total_pages): ?>
        <a href="<?= sanitise_output(build_pagination_url($page + 1)) ?>" class="next"><?= Language::get('next'); ?>
            &raquo;</a>
    <?php endif; ?>

</div>