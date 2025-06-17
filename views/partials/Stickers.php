<?php // views/partials/Stickers.php ?>

<div class="stickers-container">
    <?php foreach ($data['stickers_data'] as $sticker): ?>
        <div class="sticker">
            <button type="submit" name="add_sticker" 
            value="<?= sanitise_output($sticker['sticker_name']); ?>" style="all: unset; cursor: pointer; display: block;">
                <img src="<?= sanitise_output($sticker['sticker_location']); ?>"
                    alt="<?= sanitise_output($sticker['sticker_name']); ?>">
                <div><?= sanitise_output($sticker['sticker_name']); ?></div>
            </button>
        </div>
    <?php endforeach; ?>
</div>
