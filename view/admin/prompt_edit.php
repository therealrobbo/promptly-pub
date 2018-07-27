<div class="page-header col-md-9" id="facs-attention">
    <h1><?= $heading  ?></h1>
</div>
<?php
$button_group = array(
    'id'        => $prompt_info['id'],
    'deleted'   => $prompt_info['deleted'],
    'show_list' => true,
    'show_edit' => false,
    'show_new'  => true
);
include( 'prompt_buttons.php' );
?>

<div class="clearshort">&nbsp;</div>

<?php include( 'messages.php' ); ?>

<?php //----------------------------------- START FORM -----------------------------------------------------?>
<form action='<?= $admin_url ?>/prompt/edit' method='post' id='form1' enctype='multipart/form-data' class="form-horizontal">
    <input type="hidden" name="prompt_id"   value="<?= $prompt_info['id'] ?>" />
    <input type="hidden" name="action"    value="<?= $action ?>" />

    <?php //----------------------------- Prompt Text --------------------------------------------------------------- ?>
    <div class="form-group">
        <label for="text" class="col-sm-2 control-label">Prompt Text</label>
        <div class="col-sm-8">
            <textarea class="form-control" name="text" id="text" rows="10"><?= $prompt_info['text'] ?></textarea>
        </div>
    </div>


    <?php //----------------------------- Submit Button ---------------------------------------------------------- ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <input type='submit' id='publish' value='<?= ucfirst( $action ) ?>' class='btn btn-lg btn-default'>
        </div>
    </div>
</form>


