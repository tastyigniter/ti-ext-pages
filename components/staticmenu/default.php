<?php if ($__SELF__->menuItems()) { ?>
    <ul><?= partial($__SELF__.'::items', ['items' => $__SELF__->menuItems()]) ?></ul>
<?php } ?>