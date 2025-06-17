<?php // views/partials/Footer.php ?>

<?php if (!empty($data['statistics_data'])): ?>
    <div>
        <hr>
         <small>🟢 <?= Language::get('last_active_users'); ?></small>
                    <?php foreach ($data['statistics_data'] as $user): ?>
                        <a href="/profile/<?= sanitise_output($user['user_name']); ?>" target="_blank">
                            <small><?= sanitise_output($user['user_name']); ?></small>
                        </a>
                    <?php endforeach; ?>
    </div>
<?php endif; ?>

    </main>

    <footer style="padding:25px 0;">
        <p>
            ☞ <?= sanitise_output($data['site_data']['site_visits']); ?> <?= Language::get('visits'); ?> ☜
        </p>
        <p>
            <i>Built and maintained by <a href="https://www.dailymotion.com/video/x8uyrsw">ʘ‿ʘ</a>. Inspired by <a href="https://bearblog.dev/">Bearblog</a>.</i>
        </p>
        <small>
            <a href="https://c.tenor.com/DUHB3rClTaUAAAAC/tenor.gif" target="_blank"><?= Language::get('privacy_policy'); ?></a> |
            <a href="https://c.tenor.com/DUHB3rClTaUAAAAC/tenor.gif" target="_blank"><?= Language::get('terms_of_service'); ?></a> |
            <a href="https://c.tenor.com/DUHB3rClTaUAAAAC/tenor.gif" target="_blank"><?= Language::get('roadmap'); ?></a>
        </small>
    </footer>

    <div id="bottom"></div>

</body>

</html>