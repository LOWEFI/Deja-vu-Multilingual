<?php // views/Forums.php ?>

<!-- Forums Details -->

<ul>
    <?php foreach ($data['languages'] as $code => $label): ?>
        <li>
            <a href="/forum?language=<?= sanitise_output($code); ?>"><?= sanitise_output($label); ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<hr>

<h3>
    <a href="/posts">
        → <?= Language::get('show_posts'); ?> 🌐
    </a>
</h3>