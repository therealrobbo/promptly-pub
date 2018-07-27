<?php
/**
 *
 * Main controller for all Administrative backend pages
 *
 */


class User_admin extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

        $this->model( 'users' );
        $this->library( 'user_login' );
    }


    //----------------------- U S E R    L I S T -----------------------------------------------------------------------

    public  function index( $action = '', $user_id = 0 ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/user_admin" ) ) {

            $columns = array(
                "User Name"     => 'name',
                "Email"         => 'email',
                "Active"        => 'active',
                "Privilege"     => 'privilege'
            );

            $filters = array(
                "active_filter"  => array(
                    "label"  => "Active Users",
                    "option" => array(
                        ACTIVE_FILTER_ALL         => "All Users",
                        ACTIVE_FILTER_INACTIVE    => "Inactive Users",
                        ACTIVE_FILTER_ACTIVE      => "Active Users"
                    ),
                    "type"  => "select",
                    "span"  => "4"
                )
            );

            $search_args = array();
            $search_args['txt_keyword']        = $this->get_var( 'txt_keyword',   '' );
            $search_args['order_by']           = $this->get_var( 'order_by',      'email' );
            $search_args['order_dir']          = $this->get_var( 'order_dir',     'ASC' );
            $search_args['active_filter']      = $this->get_var( 'active_filter', ACTIVE_FILTER_ACTIVE );
            $search_args['pageno']             = $this->get_var( 'pageno',        1 );
            $search_args['items_per_page']     = 50;

            // Take any actions...
            switch( $action ) {
                case 'deactivate':
                    if ( !empty( $user_id ) ) {
                        $this->users->deactivate( $user_id );
                        $this->set_message(
                            "The user has been deactivated! &nbsp;&nbsp;&nbsp;&nbsp;<a href='" . "/pmp-admin/user_admin/reactivate/$user_id' class='btn btn-default'>Oops! Reactivate!</a>",
                            'success'
                        );
                    }
                    break;

                case 'reactivate':
                    if ( !empty( $user_id ) ) {
                        $this->users->deactivate( $user_id, false );
                        $this->set_message( "The user has been reactivated!", 'success' );
                    }
                    break;
            }


            // Now load the admins list
            $search_result = $this->users->get_list( $search_args );
            $user_count = $this->users->get_count( $search_args );
            $max_page = intval( $user_count / $search_args['items_per_page'] ) +
                ( ( $user_count % $search_args['items_per_page'] ) ? 1 : 0 );


            $this->data( 'title',         $this->gen_page_title( "Manage Admin Users" ) );

            $this->data( 'search_args',   $search_args );
            $this->data( 'search_result', $search_result );
            $this->data( 'user_count',    $user_count );
            $this->data( 'max_page',      $max_page );

            $this->data( 'filters',       $filters );
            $this->data( 'columns',       $columns );

            $this->data( 'top_pager',    $this->admin_render_pager( $search_args, $this->gPZ['backend_url'] . "/user_admin", $filters, $max_page, '', 'mini',  'txt_keyword' ) );
            $this->data( 'bottom_pager', $this->admin_render_pager( $search_args, $this->gPZ['backend_url'] . "/user_admin", $filters, $max_page, '', 'large', 'txt_keyword' ) );

            $this->data( 'colheads', $this->admin_render_column_heads( $columns, $search_args, $this->gPZ['backend_url'] . "/user_admin" ) );

            $this->data( 'template', 'user_list.php' );

            $this->asset_request( REQ_ASSET_CSS, "admin_lists" );

            $this->asset_request( REQ_ASSET_JS,  "admin_lists" );


            return ( $this->view() );
        } else {
            // We can never really get here because the conditional function dies on failure
            return( '' );
        }
    }



    //----------------------- A D M I N   E D I T ----------------------------------------------------------------------

    public  function edit( $user_id = 0, $action = 'show_form' ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/user_admin/edit" ) ) {

            if ( !empty( $user_id ) ) {

                $user_info = $this->users->get( $user_id, false, false );

                if ( empty( $user_info ) ) {
                    $user_info = $this->users->dummy();
                    $this->set_message( "The specified user id is invalid.", 'danger' );
                }
            } else {
                $user_info = $this->users->dummy();
            }


            //----------------------------------------- Route Actions --------------------------------------------------------------
            switch ( $action ) {

                //----------- Display Form ------------------
                case 'show_form':
                    if ( !empty( $user_info['id'] ) ) {
                        $action = 'edit';
                        $title  = 'Edit User';
                    } else {
                        $action = 'add';
                        $title  = 'Add New User';
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
                            if ( $updates['id']  = $this->users->add( $updates ) ) {
                                $this->set_message( "The user has been added to the database", 'success' );
                            } else {
                                $this->set_message( "Something went wrong with the database. Please report this error to the powers that be.", 'danger' );
                            }
                        } else {
                            // Update the existing poll the database...
                            if ( $this->users->update( $user_id, $updates ) ) {
                                $this->set_message( "The user has been updated successfully", 'success' );
                            } else {
                                $this->set_message( "Something went wrong with the database. Please report this error to the powers that be.", 'danger' );
                            }
                        }
                        $user_info = $this->users->get( $updates['id'] );
                    } else {
                        $user_info = $updates;
                    }
                    $action = 'edit';
                    $title = 'Edit User';
                    break;
            }

            $this->data( 'title',   $this->gen_page_title( $title ) );
            $this->data( 'heading', $title );
            $this->data( 'action',  $action );

            $this->data( 'priv_list',  $this->users->priv_names );

            $this->data( 'user_info',    $user_info );

            $this->data( 'template', 'user_edit.php' );
            return ( $this->view() );
        } else {
            return( '' );
        }
    }

    private function edit_validate( $updates, $action ) {

        $is_valid = true;
        if ( empty( $updates['name'] ) ) {

            $this->set_message( 'The user name cannot be empty!', 'danger' );
            $is_valid = false;
        }

        if ( empty( $updates['email'] ) ) {
            $this->set_message( 'The email address cannot be empty!', 'danger' );
            $is_valid = false;
        }

        if ( ( $action == 'add' ) && empty( $updates['password'] ) ) {
            $this->set_message( 'The password cannot be empty!', 'danger' );
            $is_valid = false;
        } elseif ( !empty( $updates['password'] ) && ( strlen( $updates['password'] ) < 8 ) ) {
            $this->set_message( 'The password must be 8 characters or more!', 'error' );
            $is_valid = false;
        }

        $user_crosscheck = $this->users->get_by_email( $updates['email'] );
        if ( !empty( $user_crosscheck ) && ( $user_crosscheck['id'] != $updates['id'] ) ) {
            $this->set_message( "There is already a user with the email address " . $updates['email'] . " (#" . $user_crosscheck['id'] . ")", 'danger' );
            $is_valid = false;
        }

        return( $is_valid );
    }


    private function edit_get_fields( ) {

        // Make an updates array
        $updates = array();

        // Captures the admin inputs
        $updates['id']                  = $this->get_var( 'user_id',          0 );
        $updates['name']                = strtolower( $this->get_var( 'name',             '' ) );
        $updates['password']            = $this->get_var( 'password',         '' );
        $updates['email']               = strtolower( $this->get_var( 'email',            '' ) );

        $updates['active']           = ( !empty( $this->gPZ['post_vars']['active'] ) ? '1' : '0' );
        $privileges                  = $this->get_var( 'privilege_select', null );
        $updates['privilege']        = USER_PRIV_ENDUSER;
        if ( !empty( $privileges ) ) {
            foreach( $privileges as $priv_role ) {
                $updates['privilege'] |= $priv_role;
            }
        }

        return( $updates );
    }

}