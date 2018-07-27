<div class="page-header col-md-9" id="facs-attention">
    <h1><?= $heading  ?></h1>
    <?php if ( !empty( $user_info['name'] ) ) {?><h3><?= $user_info['name'] ?></h3><?php } ?>
</div>
<?php
$button_group = array(
    'id'        => $user_info['id'],
    'active'    => $user_info['active'],
    'show_list' => true,
    'show_edit' => false,
    'show_priv' => false,
    'show_new'  => false
);
include( 'user_buttons.php' );
?>

<div class="clearshort">&nbsp;</div>

<?php include( 'messages.php' ); ?>

<?php //----------------------------------- START FORM -----------------------------------------------------?>
<form action='<?= $admin_url ?>/user_admin/edit' method='post' id='form1' enctype='multipart/form-data' class="form-horizontal">
    <input type="hidden" name="user_id"   value="<?= $user_info['id'] ?>" />
    <input type="hidden" name="action"    value="<?= $action ?>" />

    <?php //----------------------------- User Name ---------------------------------------------------------- ?>
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">User Name</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter a username" value='<?= $user_info['name'] ?>'>
        </div>
    </div>

    <?php //----------------------------- Email ---------------------------------------------------------- ?>
    <div class="form-group">
        <label for="email" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-8">
            <input type='email' id="email" name='email' class="form-control" value='<?= $user_info['email'] ?>'>
        </div>
    </div>

    <?php //----------------------------- Security Privilges ---------------------------------------------------------- ?>
    <div class="form-group">
        <label for=privilege_select" class="col-sm-2 control-label">Privileges</label>
        <div class="col-sm-8">
            <select name="privilege_select[]" multiple class="form-control">
                <?php foreach( $priv_list as $priv_id => $priv_name ) { ?>
                    <option
                        value="<?= $priv_id ?>"
                        <?= ( $priv_id & $user_info['privilege'] ) ? 'selected="selected"' : '' ?>><?= $priv_name ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <?php //----------------------------- Active ------------------------------------------------------ ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name='active' <?= ( !empty( $user_info['active'] )  ? 'checked="checked"' : '' ) ?>> Uncheck this to deactivate this user.
                </label>
            </div>
        </div>
    </div>

    <?php //----------------------------- Password ---------------------------------------------------------- ?>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">Password</label>
        <div class="col-sm-8">
            <input type='password' id="password" name='password' class="form-control" value=''>
            <strong>Leave blank if you don't want to change the user's password</strong>
        </div>
    </div>


    <?php //----------------------------- Submit Button ---------------------------------------------------------- ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <input type='submit' id='publish' value='<?= ucfirst( $action ) ?>' class='btn btn-lg btn-default'>
        </div>
    </div>
</form>


