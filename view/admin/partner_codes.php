<div class="page-header" id="facs-attention">
    <h2 class="col-md-7"><?= $page_title ?></h2>
    <div class="col-md-2" style="text-align: right">
        <button id="pc_add_item" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-plus-sign"></span> Add Partner Code</button>
    </div>
    <div class="clearshort">&nbsp;</div>
</div>

<?php if ( !empty( $message_text ) ) { ?>
    <div class="alert alert-<?= $message_type ?>"><?= $message_text ?></div>
<?php } ?>

<?php //-------------------------------------- A D D / E D I T   F O R M ---------------------------------------------?>
<form method='post' action="<?= $admin_url ?>/partner_code/"  id="pc_edit_form" <?= ( ( $edit_pc_item['pc_id'] == 0 ) ? 'style="display: none"' : '' ) ?>>
    <input type="hidden" name="action" value="<?= ( ( $edit_pc_item['pc_id'] == 0 ) ? 'add' : 'edit' ) ?>" />
    <input type="hidden" name="pc_id" value="<?= $edit_pc_item['pc_id'] ?>" />
    <input type="hidden" name="pc_order" value="<?= $edit_pc_item['pc_order'] ?>" />
    <h4 id="pc_form_heading"><?= ( ( $edit_pc_item['pc_id'] == 0 ) ? 'Add' : 'Edit' ) ?> Partner Code</h4>
    <div class="col-md-3">
        <div class="control-group">
            <label class="control-label" for="pc_name">Name</label>
            <div class="controls">
                <input type="text" id="pc_name" class="form-control" name="pc_name" placeholder="A Name for this code" value="<?= $edit_pc_item['pc_name'] ?>">
            </div><!-- controls -->
        </div><!-- control-group -->
        <div class="control-group">
            <label class="control-label" for="pc_location_id">Code Location</label>
            <div class="controls">
                <select class="form-control" name="pc_location" id="pc_location">
                    <?php foreach( $pc_locations as $pc_location_code => $pc_location_name ) { ?>
                        <option value="<?= $pc_location_code ?>" <?= ( $edit_pc_item['pc_location'] == $pc_location_code ) ? 'selected="selected"' : '' ?>>
                            <?= $pc_location_name ?>
                        </option>
                    <?php } ?>
                </select>
            </div><!-- controls -->
        </div><!-- control-group -->
        <div class="control-group">
            <label class="control-label" for="pc_excluions">Exclude From</label>
            <div class="controls">
                <select class="form-control" name="pc_exclusions[]" id="pc_exclusions" multiple="multiple">
                    <?php $current_exclusions = explode( ",", $edit_pc_item['pc_exclusions'] ) ?>
                    <?php foreach( $pc_exclusions as $pc_exclusion_code => $pc_exclusion_name ) { ?>
                        <option value="<?= $pc_exclusion_code ?>" <?= ( in_array( $pc_exclusion_code, $current_exclusions ) ? 'selected="selected"' : '' ) ?>>
                            <?= $pc_exclusion_name ?>
                        </option>
                    <?php } ?>
                </select>
            </div><!-- controls -->
        </div><!-- control-group -->
    </div><!-- span 4 -->
    <div class="col-md-6">
        <div class="control-group">
            <label class="control-label" for="pc_code">Paste in Partner Code</label>
            <div class="controls">
                <textarea name="pc_code" id="pc_code" class="form-control" rows="5"><?= $edit_pc_item['pc_code'] ?></textarea>
            </div><!-- controls -->
        </div><!-- control-group -->
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </div>
    </div><!-- span6 -->
    <div class="clearshort">&nbsp;</div>
</form>


<?php //-------------------------------------- L I S T / S O R T   F O R M -------------------------------------------?>
<?php if ( !empty( $have_parent_codes ) ) { ?>
    <form method='post' action="<?= $admin_url ?>/partner_codes/" class="form-horizontal">
        <input type="hidden" name="action" value="update_sort" />
        <div class="control-group">
            <button type="submit" class="btn btn-default">Save Sort Order</button>
        </div>
        <div class="pc_edit_list">
            <?php foreach( $pc_locations as $pc_location_code => $pc_location_name ) {
                if ( isset( $pc_section_items[$pc_location_code] ) && is_array( $pc_section_items[$pc_location_code] ) &&
                    ( count( $pc_section_items[$pc_location_code] ) > 0 ) ) { ?>
                    <h4><?= $pc_location_name ?></h4>
                    <ul class="sortable" location="<?= $pc_location_code?>">
                        <?php foreach ( $pc_section_items[$pc_location_code] as $pc_item_record ) { ?>
                            <li><div class="pc_item_info">
                                    <div class="pc_item_name"><?= $pc_item_record['pc_name'] ?> </div>
                                    <div class="pc_item_options btn-group">
                                        <a href="<?= $admin_url ?>/partner_code/edit_fetch/<?= $pc_item_record['pc_id'] ?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                        <a href="<?= $admin_url ?>/partner_code/delete/<?= $pc_item_record['pc_id'] ?>" class="pc_delete_button btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                                    </div>
                                    <div class="clearshort">&nbsp;</div>
                                    <input type="hidden" class="pc_sort" name="sort_order[<?= $pc_item_record['pc_id'] ?>]" value="<?= $pc_item_record['pc_order'] ?>" />
                                    <input type="hidden" class="pc_location" name="location_id[<?= $pc_item_record['pc_id'] ?>]" value="<?= $pc_item_record['pc_location'] ?>" />
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } } ?>
        </div>
        <div class="control-group">
            <button type="submit" class="btn  btn-default">Save Sort Order</button>
        </div>
    </form>
<?php } else { ?>
    <div class="hero-unit">There are currently no partner codes defined</div>
<?php } ?>