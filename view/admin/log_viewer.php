<div class="page-header" id="facs-attention">
    <h2>View Logs</h2>
</div>


<?php if ( !empty( $message_text ) ) { ?>
    <div class="alert alert-<?= $message_type ?>"><?= $message_text ?></div>
<?php } ?>

<div id="log-wrapper" data-view-url="<?= $admin_url ?>/log_viewer">
    <?php //------------------------------------- CHANGE LOGS ----------------------------------------------------------- ?>
    <form id="log_controls" class="form-inline">
        <div class="form-group">
        <select class="form-control" id="log_type_select">
        <?php foreach( $log_types as $log_type_index => $log_name ) { ?>
            <option value="<?= $log_type_index ?>" <?= ( $log_type_index == $log_type ) ? 'selected="selected"' : '' ?>><?= $log_name ?></option>
        <?php } ?>
        </select>
        </div>
    <?php if ( !empty( $log_list ) ) { ?>
        <div class="form-group">
            <select class="form-control" id="log_select">
                <?php foreach( $log_list as $log_date ) { ?>
                    <option value="<?= $log_date ?>"  <?= ( $log_date == $date ) ? 'selected="selected"' : '' ?>><?= substr( $log_date, 0, 4 ) . "-" . substr( $log_date, 4, 2 ) . "-" . substr( $log_date, 6, 2 ) ?></option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
    <button id="log-fullscreen" class="btn btn-default"><span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span></button>
    <button id="log-download" class="btn btn-default"><span class="glyphicon glyphicon-download" aria-hidden="true"></span> Download</button>
    <div class="clearshort"></div>
    </form>

    <div id="log_window"><?= $log_contents ?></div>
</div>
