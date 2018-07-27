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
$button_group['class']     = isset( $button_group['class'] )     ? $button_group['class']     : '';
$button_group['show_list'] = isset( $button_group['show_list'] ) ? $button_group['show_list'] : false;
$button_group['show_new']  = isset( $button_group['show_new'] )  ? $button_group['show_new']  : false;
$button_group['id']        = isset( $button_group['id'] )        ? $button_group['id']        : 0;
$button_group['show_edit'] = isset( $button_group['show_edit'] ) ? $button_group['show_edit'] : false;
$button_group['active']    = isset( $button_group['active'] )    ? $button_group['active']    : 1;
$button_group['size']      = isset( $button_group['size'] )      ? $button_group['size']      : 'xs';

?>
<div class="btn-group <?= $button_group['class'] ?>">
    <?php if ( !empty( $button_group['show_list'] ) ) { ?>
        <a href="<?= $admin_url ?>/user_admin" class="btn btn-default btn-<?= $button_group['size'] ?>"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> User List</a>
    <?php } ?>
    <?php if ( !empty( $button_group['show_new'] ) ) { ?>
        <a href="<?= $admin_url ?>/user_admin/edit" class="btn btn-<?= $button_group['size'] ?> btn-warning"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> New User</a>
    <?php } ?>
    <?php if ( !empty( $button_group['id'] ) ) { ?>
        <?php if ( !empty( $button_group['show_edit'] ) ) { ?>
            <a href="<?= $admin_url ?>/user_admin/edit/<?= $button_group['id'] ?>" class="btn btn-default btn-<?= $button_group['size'] ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit</a>
        <?php } ?>
        <?php if ( !empty( $button_group['active'] ) ) { ?>
            <a href="<?= $admin_url ?>/user_admin/deactivate/<?= $button_group['id'] ?>" class="btn btn-<?= $button_group['size'] ?> btn-danger"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Deactivate</a>
        <?php } else { ?>
            <a href="<?= $admin_url ?>/user_admin/reactivate/<?= $button_group['id'] ?>" class="btn btn-<?= $button_group['size'] ?> btn-info"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> Reactivate</a>
        <?php } ?>
    <?php } ?>
</div><!-- btn-group -->
