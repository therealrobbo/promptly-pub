<div class="page-header" id="prompt-attention">
    <h1>Login</h1>
</div>

<?php include( 'messages.php' ); ?>

<form action='<?= $admin_url ?>/' method='post' class="form-horizontal" style="width:600px;margin:auto">
    <input type='hidden' name='redir' value="<?= $redir ?>">

    <div class="form-group">
        <label for='username' class="col-sm-5 control-label">Email:</label>
        <div class="col-sm-7">
            <input type='text' class='login form-control' name='username' value="<?= $username ?>">
        </div>
    </div>

    <div class="form-group">
        <label for='password' class="col-sm-5 control-label">Password:</label>
        <div class="col-sm-7">
            <input type='password'  class="form-control" name='password' class='login'>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-7">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name='permanent' value='1' class='clear'<?= ( $permanent  ? 'checked="checked"' : '' )?>> Remember my info
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-5 col-sm-7">
            <input type='submit' value='Login' class="btn btn-large btn-primary">&nbsp;
        </div>
    </div>

</form>
