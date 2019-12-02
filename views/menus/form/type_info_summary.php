<p class="card-title font-weight-bold mb-0"><?= e(lang($item->title)) ?></p>
<?php if ($item->parent) { ?>
    <span class="text-muted">Parent: </span><?= e(lang($item->parent->title)); ?>&nbsp;&nbsp;
<?php } ?>
<span class="text-muted">Type: </span><?= $item->type; ?>
<div
    data-properties="<?= htmlspecialchars(json_encode($item->toArray()), ENT_QUOTES); ?>"
></div>