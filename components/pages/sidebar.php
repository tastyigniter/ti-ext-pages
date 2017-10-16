<div id="page-box" class="module-box">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $pagesTitle; ?></h3>
        </div>
        <div class="list-group list-group-responsive">
            <?php foreach ($pagesList as $page) { ?>
                <a
                    class="list-group-item <?= ($activePageId == $page['page_id']) ? 'active' : ''; ?>"
                    href="<?= site_url($page['permalink_slug']); ?>"
                ><i class="fa fa-angle-right"></i>
                    &nbsp;&nbsp;<?= $page['name']; ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>