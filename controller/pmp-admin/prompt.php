<?php
/**
 *
 * Main controller for all Administrative backend pages
 *
 */


class Prompt extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

        $this->model( 'prompts' );
    }


    //----------------------- P R O M P T   L I S T --------------------------------------------------------------------

    public  function index( $action = '', $prompt_id = 0 ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/prompt" ) ) {

            $this->library( 'util' );

            $columns = array(
                "Excerpt"   => '',
                "Added"     => 'date_added',
                "Updated"   => 'date_updated',
                "Use(d) On" => 'use_date',
                "Trashed"   => 'deleted'
            );

            $filters = array(
                "trash_filter"  => array(
                    "label"  => "Trashed?",
                    "option" => array(
                        PS_TRASHED_ALL    => "All Prompts",
                        PS_TRASHED_YES    => "Trashed Prompts",
                        PS_TRASHED_NO     => "Active Prompts"
                    ),
                    "type"  => "select",
                    "span"  => "4"
                )
            );

            $search_args = array();
            $search_args['txt_keyword']        = $this->get_var( 'txt_keyword',   '' );
            $search_args['order_by']           = $this->get_var( 'order_by',      'date_updated' );
            $search_args['order_dir']          = $this->get_var( 'order_dir',     'DESC' );
            $search_args['trash_filter']       = $this->get_var( 'trash_filter',  PS_TRASHED_NO );
            $search_args['pageno']             = $this->get_var( 'pageno',        1 );
            $search_args['items_per_page']     = 50;

            // Take any actions...
            switch( $action ) {
                case 'delete':
                    if ( !empty( $prompt_id ) ) {
                        $this->prompts->delete( $prompt_id );
                        $this->set_message(
                            "The prompt has been deleted! &nbsp;&nbsp;&nbsp;&nbsp;<a href='" . "/pmp-admin/prompt/undelete/$prompt_id' class='btn btn-default'>Oops! Undelete!</a>",
                            'success'
                        );
                    }
                    break;

                case 'undelete':
                    if ( !empty( $prompt_id ) ) {
                        $this->prompts->delete( $prompt_id, false );
                        $this->set_message( "The user has been undeleted! &nbsp;&nbsp;&nbsp;&nbsp;<a href='" . "/pmp-admin/prompt/delete/$prompt_id' class='btn btn-default'>Oops! Delete!</a>", 'success' );
                    }
                    break;
            }


            // Now load the admins list
            $search_result = $this->prompts->get_list( $search_args );
            $user_count = $this->prompts->get_count( $search_args );
            $max_page = intval( $user_count / $search_args['items_per_page'] ) +
                ( ( $user_count % $search_args['items_per_page'] ) ? 1 : 0 );


            $this->data( 'title',         $this->gen_page_title( "Manage Writing Prompts" ) );

            $this->data( 'today',         date( "Y-m-d H:i:s" ) );

            $this->data( 'search_args',   $search_args );
            $this->data( 'search_result', $search_result );
            $this->data( 'prompt_count',  $user_count );
            $this->data( 'max_page',      $max_page );

            $this->data( 'filters',       $filters );
            $this->data( 'columns',       $columns );


            $this->data( 'top_pager',    $this->admin_render_pager( $search_args, $this->gPZ['backend_url'] . "/prompt", $filters, $max_page, '', 'mini',  'txt_keyword' ) );
            $this->data( 'bottom_pager', $this->admin_render_pager( $search_args, $this->gPZ['backend_url'] . "/prompt", $filters, $max_page, '', 'large', 'txt_keyword' ) );

            $this->data( 'colheads', $this->admin_render_column_heads( $columns, $search_args, $this->gPZ['backend_url'] . "/prompt" ) );

            $this->data( 'template', 'prompt_list.php' );

            $this->asset_request( REQ_ASSET_CSS, "admin_lists" );

            $this->asset_request( REQ_ASSET_JS,  "admin_lists" );


            return ( $this->view() );
        } else {
            // We can never really get here because the conditional function dies on failure
            return( '' );
        }
    }



    //----------------------- P R O M P T   E D I T --------------------------------------------------------------------

    public  function edit( $prompt_id = 0, $action = 'show_form' ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/prompt/edit/" . $prompt_id ) ) {

            if ( !empty( $prompt_id ) ) {

                $prompt_info = $this->prompts->get( $prompt_id );

                if ( empty( $prompt_info ) ) {
                    $prompt_info = $this->prompts->dummy();
                    $this->set_message( "The specified user id is invalid.", 'danger' );
                }
            } else {
                $prompt_info = $this->prompts->dummy();
            }


            //----------------------------------------- Route Actions --------------------------------------------------------------
            switch ( $action ) {

                //----------- Display Form ------------------
                case 'show_form':
                    if ( !empty( $prompt_info['id'] ) ) {
                        $action = 'edit';
                        $title  = 'Edit Prompt';
                    } else {
                        $action = 'add';
                        $title  = 'Add New Prompt';
                    }
                    break;


                //----------- Respond to form submit ------------
                case 'add':
                case 'edit':

                    // Capture the form inputs.
                    $updates = $this->edit_get_fields();

                    // Validate the form inputs.
                    if ( $this->edit_validate( $updates, $action ) ) {

                        // If we're adding a user...
                        if ( $action == 'add' ) {

                            // Add the user to the database...
                            if ( $updates['id']  = $this->prompts->add( $updates ) ) {
                                $this->set_message( "The writing prompt has been added to the database", 'success' );
                            } else {
                                $this->set_message( "Something went wrong with the database. Please report this error to the powers that be.", 'danger' );
                            }
                        } else {
                            // Update the existing poll the database...
                            if ( $this->prompts->update( $prompt_id, $updates ) ) {
                                $this->set_message( "The writing prompt has been updated successfully", 'success' );
                            } else {
                                $this->set_message( "Something went wrong with the database. Please report this error to the powers that be.", 'danger' );
                            }
                        }
                        $prompt_info = $this->prompts->get( $updates['id'] );
                    } else {
                        $prompt_info = $updates;
                    }
                    $action = 'edit';
                    $title = 'Edit Prompt';
                    break;
            }

            $this->data( 'title',   $this->gen_page_title( $title ) );
            $this->data( 'heading', $title );
            $this->data( 'action',  $action );

            $this->data( 'prompt_info',    $prompt_info );

            $this->data( 'template', 'prompt_edit.php' );
            return ( $this->view() );
        } else {
            return( '' );
        }
    }

    private function edit_validate( $updates, $action ) {

        $is_valid = true;
        if ( empty( $updates['text'] ) ) {

            $this->set_message( 'The writing prompt must have some text, of course!', 'danger' );
            $is_valid = false;
        } elseif ( strlen( $updates['text'] ) < 50 ) {
            $this->set_message( "That's a little short for a writing prompt, isn't it?", 'danger' );
            $is_valid = false;
        }

        return( $is_valid );
    }


    private function edit_get_fields( ) {

        // Make an updates array
        $updates = array();

        // Captures the admin inputs
        $updates['id']                  = $this->get_var( 'prompt_id',        0 );
        $updates['text']                = $this->get_var( 'text',             '' );

        return( $updates );
    }

}