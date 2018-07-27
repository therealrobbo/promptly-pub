<form action="<?= $url ?>" method='post' class="admin_pager_form">
    <?= $arg_string ?>
    <input type="hidden" name="pageno" value="<?= $pager_vars['page_val'] ?>">
    <a href="#"><?= $pager_vars['page_label'] ?></a>
</form>