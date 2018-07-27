<div class="page-header col-md-10" id="admin_attention"  >
    <h1>Manage Users</h1>
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
include( 'user_buttons.php' );
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
                <label>By Name:</label>
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
        <?php foreach( $search_result as $user_rec ) { ?>
            <tr>
                <td>
                    <a href="<?= $admin_url ?>/user_admin/edit/<?= $user_rec['id'] ?>"><?= $user_rec['name'] ?></a>
                </td>
                <td>
                    <a href="<?= $admin_url ?>/user_admin/edit/<?= $user_rec['id'] ?>"><?= $user_rec['email'] ?></a>
                </td>
                <td <?= ( $user_rec['active'] == 1 ) ? '' : 'class="alert-error"' ?>><?= ( $user_rec['active'] == 1 ) ? 'YES' : 'NO' ?></td>
                <td>
                    <?= $this->users->list_priv( $user_rec['privilege'] ) ?>
                </td>
                <td>
                    <?php
                    $button_group = array(
                        'id'        => $user_rec['id'],
                        'active'    => $user_rec['active'],
                        'show_list' => false,
                        'show_edit' => true,
                        'show_priv' => true
                    );
                    include( 'user_buttons.php' );
                    ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php } else { ?>
    <div class="hero-unit alert-error">
        <h2>We're sorry. No users in the database matched your search criteria</h2>
    </div>
<?php } ?>
<?php if ( $max_page > 1 ) { ?>
    <div class="well">
        <div class="row-fluid show-grid">
            <?= $bottom_pager ?>
        </div>
    </div>
<?php } ?>