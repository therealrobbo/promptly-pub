<?php
/**
 *
 * Main controller for partner codes
 *
 */


class Partner_code extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

    }


    // ------------------------------------- PARTNER CODES -------------------------------------------------------------

    public function index( $action = 'form', $pc_id = 0, $sort_order = 0, $location_id = 0 ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/patner_code" ) ) {

            $this->model( 'partner_codes' );
            $edit_pc_item = $this->partner_codes_get_inputs( true );

            switch( $action ) {
                case 'add':
                    $pc_form_inputs = $this->partner_codes_get_inputs( );
                    $pc_id = $this->partner_codes->add( $pc_form_inputs );
                    if ( !empty( $pc_id ) )
                        $this->set_message( "Partner Code <strong>" . $pc_form_inputs['pc_name'] . "</strong> added to database", 'success' );
                    else
                        $this->set_message( "Something went wrong with the database and the partner code was not added. Please contact the powers that be.", 'error' );
                    break;

                case 'edit':
                    $pc_form_inputs = $this->partner_codes_get_inputs( );
                    $this->partner_codes->update( $pc_form_inputs );
                    $this->set_message( "Partner Code <strong>" . $pc_form_inputs['nav_name'] . "</strong> updated", 'success' );
                    break;

                case 'edit_fetch':
                    $edit_pc_item = $this->partner_codes->get_code_item( $pc_id );
                    if ( empty( $edit_pc_item ) ) {
                        $edit_pc_item = $this->partner_codes_get_inputs( true );
                    }
                    break;

                case 'delete':
                    if ( $this->partner_codes->delete( $pc_id ) )
                        $this->set_message( "Partner Code Deleted", 'success' );
                    else
                        $this->set_message( "Deletion failed: <br />" . $this->db->error( ), 'error' );
                    break;

                case 'update_sort':
                    $this->partner_codes->update_sort( $sort_order, $location_id );
                    $this->set_message( "Partner Code sort order updated", 'success' );
                    break;
            }


            $pc_section_items = array();

            $have_parent_codes = false;
            foreach( $this->partner_codes->locations as $location_id => $location_name  ) {

                // Get the current list of nav items
                $pc_section_items[$location_id] = $this->partner_codes->get_all( $location_id );
                if ( isset( $pc_section_items[$location_id] ) && is_array( $pc_section_items[$location_id] ) &&
                    ( count( $pc_section_items[$location_id] ) > 0 ) ) {
                    $have_parent_codes = true;
                }
            }

            $page_title = "Edit Partner Codes";
            $this->data( 'title',         $this->gen_page_title( $page_title ) );
            $this->data( 'page_title',    $page_title );

            $this->data( 'edit_pc_item',      $edit_pc_item );
            $this->data( 'pc_locations',      $this->partner_codes->locations );
            $this->data( 'pc_exclusions',     $this->partner_codes->exclusions );
            $this->data( 'have_parent_codes', $have_parent_codes );
            $this->data( 'pc_section_items',  $pc_section_items );

            $this->data( 'template', 'partner_codes.php' );

            $this->asset_request( REQ_ASSET_CSS, 'admin_pc_edit' );
            $this->asset_request( REQ_ASSET_JS,  'jquery-ui' );
            $this->asset_request( REQ_ASSET_JS,  'admin_pc_edit' );

            return( $this->view( ) );
        } else {
            return( '' );
        }
    }

    /**
     * Get the form values
     *
     * @param bool $default_only - TRUE to ignore user submitted values and return a default formatted nav_item record
     * @return array
     */
    private function partner_codes_get_inputs( $default_only = false ) {

        $form_inputs = array (
            'pc_id'         => 0,
            'pc_name'       => '',
            'pc_code'       => '',
            'pc_location'   => PC_LOCATION_AFTER_BODY_CLOSE,
            'pc_order'      => 0,
            'pc_exclusions' => PC_EXCLUDE_NONE
        );

        // If we're only loading defaults we could stop. If we're not then
        // we want to load the values from the form/$_REQUEST super
        if ( !$default_only ) {

            // go through all the fieleds
            foreach( $form_inputs as $key => $value ) {

                $form_inputs[$key] = $this->get_var( $key, $form_inputs[$key] );
                // If the request array is set for this field
            }

            $form_inputs['pc_exclusions'] = ( isset( $_REQUEST['pc_exclusions'] ) ? implode( ",", $_REQUEST['pc_exclusions'] ) : '' );
        }

        return( $form_inputs );
    }


}