<?php
/**
 * Before including this, set...
 *
 * $button_group['id']         =  the admin ID
 * $button_group['active']     =  the active status of the admin (if ID is specified)
 * $button_group['class']      =  and classes to wrap the button group in
 * $button_group['show_list']  = TRUE to show a button to the main admin list
 * $button_group['show_edit']  = TRUE to show a button to the main admin list
 * $button_group['show_priv']  = TRUE to show a button to the main admin list
 */
$button_group              = isset( $button_group )              ? $button_group              : array();
$button_group['id']        = isset( $button_group['id'] )        ? $button_group['id']        : 0;
$button_group['deleted']   = isset( $button_group['deleted'] )   ? $button_group['deleted']   : 0;
$button_group['class']     = isset( $button_group['class'] )     ? $button_group['class']     : '';
$button_group['size']      = isset( $button_group['size'] )      ? $button_group['size']      : 'xs';
$button_group['show_list'] = isset( $button_group['show_list'] ) ? $button_group['show_list'] : false;
$button_group['show_new']  = isset( $button_group['show_new'] )  ? $button_group['show_new']  : false;
$button_group['show_edit'] = isset( $button_group['show_edit'] ) ? $button_group['show_edit'] : false;
?>
<div class="btn-group <?= $button_group['class'] ?>">
    <?php if ( !empty( $button_group['show_list'] ) ) { ?>
        <a href="<?= $admin_url ?>/prompt" class="btn btn-default btn-<?= $button_group['size'] ?>"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Prompt List</a>
    <?php } ?>
    <?php if ( !empty( $button_group['show_new'] ) ) { ?>
        <a href="<?= $admin_url ?>/prompt/edit" class="btn btn-<?= $button_group['size'] ?> btn-warning"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> New Prompt</a>
    <?php } ?>
    <?php if ( !empty( $button_group['id'] ) ) { ?>
        <?php if ( !empty( $button_group['show_edit'] ) ) { ?>
            <a href="<?= $admin_url ?>/prompt/edit/<?= $button_group['id'] ?>" class="btn btn-default btn-<?= $button_group['size'] ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit</a>
        <?php } ?>
        <?php if ( empty( $button_group['deleted'] ) ) { ?>
            <a href="<?= $admin_url ?>/prompt/delete/<?= $button_group['id'] ?>" class="btn btn-<?= $button_group['size'] ?> btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</a>
        <?php } else { ?>
            <a href="<?= $admin_url ?>/prompt/undelete/<?= $button_group['id'] ?>" class="btn btn-<?= $button_group['size'] ?> btn-info"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> Undelete</a>
        <?php } ?>
    <?php } ?>
</div><!-- btn-group -->
