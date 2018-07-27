<div class="page-header col-md-10" id="admin_attention"  >
    <h1>Manage Writing Prompts</h1>
</div>
<?php
$button_group = array(
    'id'        => 0,
    'active'    => 0,
    'show_list' => false,
    'show_edit' => false,
    'show_priv' => false,
    'show_new'  => true,
    'class'     => 'col-md-2',
    'size'      => 'lg'
);
include( 'prompt_buttons.php' );
?>
<div class="clearshort"></div>

<?php include( 'messages.php' ); ?>

<div class="well csl-well">
    <form action='' method='post' class="form-inline">
        <input type="hidden" name="order_by" value="<?= $search_args['order_by'] ?>" />
        <input type="hidden" name="order_dir" value="<?= $search_args['order_dir'] ?>" />
        <input type="hidden" name="pageno" value="1" />
        <div class="row show-grid">
            <div class="col-md-6">
                <label>Search Text:</label>
                <input type='text' class="form-control" name='txt_keyword' value="<?= $search_args['txt_keyword'] ?>">
                <input type='submit' value='Search' class='btn btn-primary btn-sm'>
            </div>
            <div class="col-md-2">
                <a href="#" class="btn btn-default btn-sm"  onclick="$('#more-filters').toggle(100)"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Filter Results</a>
            </div>

        </div>
        <div class="row show-grid" id="more-filters" style="display: none">
            <?php foreach( $filters as $select_name => $filter_settings ) { ?>
                <div class="col-md-3">
                    <label><?= $filter_settings['label'] ?></label>
                    <select class="form-control" name="<?= $select_name ?>" class="input-small">
                        <?php foreach( $filter_settings['option'] as $opt_value => $opt_label ) { ?>
                            <?php
                            if ( is_string( $opt_value ) )
                                $comparison = ( strval( $search_args[$select_name] ) == $opt_value );
                            else
                                $comparison = ( intval( $search_args[$select_name] ) == $opt_value );
                            ?>
                            <option value="<?= $opt_value ?>" <?= ( $comparison ? 'selected="selected"' : '' ) ?>><?= $opt_label ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
        </div>
    </form>
    <?= $top_pager ?>
    <div class="clearshort"></div>
</div><!-- well -->

<?php if ( !empty( $search_result ) && is_array( $search_result ) && ( count( $search_result ) > 0 ) ) { ?>
    <table class="table table-condensed table-bordered admin_table">
        <thead>
        <?php foreach( $columns as $column_title => $sort_field ) { ?>
            <?= $colheads[$column_title] ?>
        <?php } ?>
        <th>Actions</th>
        </thead>
        <tbody>
        <?php foreach( $search_result as $prompt_rec ) { ?>
            <tr>
                <td>
                    <?= substr( strip_tags( $prompt_rec['text'] ), 0, 50 ) . "..." ?>
                </td>
                <td>
                    <?= $this->util->human_date( $prompt_rec['date_added'] ) ?>
                </td>
                <td>
                    <?= $this->util->human_date( $prompt_rec['date_updated'] ) ?>
                </td>
                <td <?= ( ( ( $prompt_rec['use_date'] != BLANK_DATE ) && ( $today >= $prompt_rec['use_date'] ) ) ? "class='alert-warning'" : "" ) ?> >
                    <?= $this->util->human_date( $prompt_rec['use_date'] ) ?>
                </td>
                <td <?= ( $prompt_rec['deleted'] == 1 ) ? 'class="alert-error"' : '' ?>><?= ( $prompt_rec['deleted'] == 1 ) ? 'YES' : 'NO' ?></td>
                <td>
                    <?php
                    $button_group = array(
                        'id'        => $prompt_rec['id'],
                        'deleted'   => $prompt_rec['deleted'],
                        'show_list' => false,
                        'show_edit' => true
                    );
                    include( 'prompt_buttons.php' );
                    ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php } else { ?>
    <div class="hero-unit alert-error">
        <h2>We're sorry. No prompts in the database matched your search criteria</h2>
    </div>
<?php } ?>
<?php if ( $max_page > 1 ) { ?>
    <div class="well">
        <div class="row-fluid show-grid">
            <?= $bottom_pager ?>
        </div>
    </div>
<?php } ?>