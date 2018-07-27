<div id="writing_widgets">
    <span id="prompt_state" data-state="default" class="label label-default">write!</span>
    <div id="coffee_control" class="btn-group" role="group" aria-label="...">
        <button id="coffee_control_rate" type="button" class="btn btn-sm btn-default" title="rate today's prompt"><span class="glyphicon glyphicon-star"></span></button>
        <button id="coffee_control_login" type="button" class="btn btn-sm btn-default" title="login"><span class="glyphicon glyphicon-user"></span></button>

        <?php if ( isset( $coffee_control_dl_types ) ) { ?>
        <div class="btn-group" role="group">
            <button id="coffee_control_download" type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" title="download your work" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-save"></span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <?php foreach( $coffee_control_dl_types as $dl_type => $dl_name ) { ?>
                    <li><a class="coffee_download" data-type="<?= $dl_type ?>" href="#"><?= $dl_name ?></a></li>
                <?php } ?>
            </ul>
            <form id="coffee_download_form" method="post" action="/download">
                <input type="hidden" name="sample_text" value="" />
                <input type="hidden" name="type" value="" />
            </form>
        </div>
        <?php } ?>
        <button id="coffee_conrol_view" type="button" class="btn btn-sm btn-info" title="view prompt"><span class="glyphicon glyphicon-eye-open"></span></button>
    </div>
</div>
