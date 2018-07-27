<?php
if ( !empty( $pager_vars ) ) {

    $max_pager_blocks = 5;

    $url = $pager_vars['list_page'];
    $arg_string = '';
    if ( !empty( $pager_vars['text_filter'] ) ) {
        $arg_string .= "<input type='hidden' name='" . $pager_vars['text_filter'] . "' value='" . htmlentities( $pager_vars['search_args'][$pager_vars['text_filter']] ) . "' />";
    }
    foreach( $pager_vars['filters'] as $filter_name => $filter_settings ) {
        $arg_string .= "<input type='hidden' name='" . $filter_name . "' value='" . htmlentities( $pager_vars['search_args'][$filter_name] ) . "' />";
    }

    if ( isset( $pager_vars['additional_args'] ) && is_array( $pager_vars['additional_args'] ) ) {
        foreach( $pager_vars['additional_args'] as $arg_name => $arg_val ) {
            $arg_string .= "<input type='hidden' name='" . $arg_name . "' value='" . htmlentities( $arg_val ) . "' />";
        }
    }

    $arg_string .= "<input type='hidden' name='order_by' value='" . htmlentities( $pager_vars['search_args']['order_by'] ) . "' />";
    $arg_string .= "<input type='hidden' name='order_dir' value='" . htmlentities( $pager_vars['search_args']['order_dir'] ) . "' />";

    $start = max( 1, min( ( $pager_vars['search_args']['pageno'] - 2 ), ( $pager_vars['max_page'] - 4 ) ) );
    if ( $pager_vars['max_page'] > 1 ) { ?>
    <div class="span<?= $pager_vars['pager_span'] ?> admin_pager">
        <div class="pagination pagination-<?= $pager_vars['pager_size'] ?>" style="margin: 0">
            <ul>
                <?php if ( $start != 1 ) { ?><li><?php $pager_vars['page_val'] = 1; $pager_vars['page_label'] = "&#x25C4&#x25C4"; include( 'pager_single.php' ); ?></li><?php } ?>
                <?php if ( $pager_vars['search_args']['pageno'] > 1 ) { ?><li><?php $pager_vars['page_val'] = ( $pager_vars['search_args']['pageno'] - 1 ); $pager_vars['page_label'] = "&#x25C4"; include( 'pager_single.php' ); ?></li><?php } ?>
                <?php for ( $display_page = $start; $display_page < ( $start + $max_pager_blocks ); $display_page++ ) { ?>
                    <li <?= ( $display_page == $pager_vars['search_args']['pageno']) ? 'class="active"' : '' ?>><?php $pager_vars['page_val'] = $display_page; $pager_vars['page_label'] = $display_page; include( 'pager_single.php' ); ?></li>
                    <?php if ( $display_page == $pager_vars['max_page'] ) break; ?>
                <?php } ?>
                <?php if ( $pager_vars['search_args']['pageno'] < $pager_vars['max_page'] ) { ?><li><?php $pager_vars['page_val'] = ( $pager_vars['search_args']['pageno'] + 1 ); $pager_vars['page_label'] = "&#x25BA"; include( 'pager_single.php' ); ?></li><?php } ?>
                <?php if ( $pager_vars['max_page'] >= ( $start + $max_pager_blocks ) ) { ?><li><?php $pager_vars['page_val'] = $pager_vars['max_page']; $pager_vars['page_label'] = "&#x25BA&#x25BA"; include( 'pager_single.php' ); ?></li><?php } ?>
            </ul>
        </div>
    </div>
    <div class="clearshort"></div>
    
<?php } 

 } ?>
