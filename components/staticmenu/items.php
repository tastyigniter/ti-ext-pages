<?php foreach ($items as $item) { ?>
    <li role="presentation"
        class="<?= $item->isActive ? 'active' : '' ?> <?= $item->isChildActive ? 'child-active' : '' ?>">
        <?php if ($item->url) { ?>
            <a href="<?= $item->url ?>" <?= isset($item->config['isExternal']) ? 'target="_blank"' : '' ?>>
                <?= $item->title ?>
            </a>
        <?php } else { ?>
            <span><?= $item->title ?></span>
        <?php } ?>

        <?php if ($item->items) { ?>
            <ul><?= partial($__SELF__.'::items', ['items' => $item->items]) ?></ul>
        <?php } ?>
    </li>
<?php } ?>