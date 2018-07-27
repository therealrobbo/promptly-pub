<th class="admin_active_column <?= ( ( $colhead_args['sort_field'] == $colhead_args['search_args']['order_by'] ) ? 'active' : '' ) ?>" >
    <?php if ( !empty( $colhead_args['sort_field'] ) ) { ?>
    <form action="<?= $colhead_args['url'] ?>" method='post' class="admin_header_form">
        <input type="hidden" name="txt_shop_name" value="<?= htmlentities( $colhead_args['search_args']['txt_shop_name'] ) ?>" />
        <input type="hidden" name="active_filter" value="<?= htmlentities( $colhead_args['search_args']['active_filter'] ) ?>" />
        <input type="hidden" name="open_closed_filter" value="<?= htmlentities( $colhead_args['search_args']['open_closed_filter'] ) ?>" />
        <input type="hidden" name="trash_filter" value="<?= htmlentities( $colhead_args['search_args']['trash_filter'] ) ?>" />
        <input type="hidden" name="claim_filter" value="<?= htmlentities( $colhead_args['search_args']['claim_filter'] ) ?>" />
        <input type="hidden" name="country_filter" value="<?= htmlentities( $colhead_args['search_args']['country_filter'] ) ?>" />
        <input type="hidden" name="order_by" value="<?= $colhead_args['sort_field'] ?>" />
        <input type="hidden" name="order_dir" value="<?= ( ( $colhead_args['sort_field'] == $colhead_args['search_args']['order_by'] ) ? $colhead_args['reverse_dir'] : $colhead_args['search_args']['order_dir'] ) ?>" />
        <a href="#"><?= $colhead_args['column_title'] ?><?= ( ( $colhead_args['sort_field'] == $colhead_args['search_args']['order_by'] ) ? $colhead_args['icon_dir'] : '' ) ?></a>
    </form>
    <?php } else { ?>
        <?= $colhead_args['column_title'] ?><?= ( ( $colhead_args['sort_field'] == $colhead_args['search_args']['order_by'] ) ? $colhead_args['icon_dir'] : '' ) ?>
    <?php } ?>
</th>
