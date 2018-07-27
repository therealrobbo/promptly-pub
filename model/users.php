<?php

/**
 * Model for Retailer users who manage their own store info
 */

define( 'USER_PRIV_ENDUSER', 1 );
define( 'USER_PRIV_ADMIN',   2048 );

define( 'ACTIVE_FILTER_ALL',     -1 );
define( 'ACTIVE_FILTER_INACTIVE', 0 );
define( 'ACTIVE_FILTER_ACTIVE',   1 );


class Users extends PZ_Model {

    private $user_table       = 'users';
    private $field_prefix     = 'user_';
    private $field_dictionary = array(
        'id'                => 0,
        'name'              => '',
        'password'          => '',
        'date_joined'       => BLANK_DATE,
        'privilege'         => USER_PRIV_ENDUSER,
        'email'             => '',
        'url'               => '',
        'active'            => 1
    );

    public $priv_names = array(
        USER_PRIV_ENDUSER => "End User",
        USER_PRIV_ADMIN   => "Admin Users"
    );

    function __construct(  ) {
        parent::__construct(  );

    }

    private function field_is_in_dictionary( $field_name ) {
        return( isset( $this->field_dictionary[$field_name] ) );
    }

    private function strip_prefix( $user_rec ) {
        $new_user_rec = array();
        foreach( $user_rec as $key => $value ) {
            $stripped_key = str_replace( $this->field_prefix, '', $key );
            if ( $this->field_is_in_dictionary( $stripped_key ) ) {
                $new_user_rec[$stripped_key] = $value;
            }
        }

        return( $new_user_rec );
    }

    private function query_field( $field_suffix ) {
        return( $this->user_table . "." . $this->field_prefix . $field_suffix );
    }

    function dummy( ) {
        $return_rec = array( );
        foreach( $this->field_dictionary  as $key => $value ) {
            $return_rec[$key] = $value;
        }

        return( $return_rec );
    }

    function add( $update_fields = null ) {

        $set_part = '';
        foreach( $update_fields as $key => $value ) {
            if ( $this->field_is_in_dictionary( $key ) ) {
                // We need to set the password as a hash
                if ( $key == 'password' ) {
                    $password = $value;
                    continue;
                }
                if ( $key == 'id' ) {
                    continue;
                }
                if ( $key == 'email' ) {
                    $email = $value;
                }

                if ( empty( $set_part ) ) {
                    $set_part = "SET ";
                } else {
                    $set_part .= ", ";
                }

                $set_part .= $this->query_field( $key ) . " = '" . $this->gCI->db->escape_string( $value ) .  "' ";
            }
        }
        $set_part .= ", " . $this->query_field( 'password' ) . " = '" . $this->gCI->db->escape_string( $this->password_hash( $email, $password ) ) .  "' ";

        if ( empty( $set_part ) ) {
            return ( 0 );
        } else {

            $set_part .= ", " . $this->query_field( 'date_joined' ) . " = now()";


            // Now add the corresponding CSL Admin record...
            $query2 = "INSERT INTO " . $this->user_table . " " .
                $set_part;
            $this->gCI->db->query( $query2 );
            return ( $this->gCI->db->insert_id() );
        }
    }


    /**
     * Get the data for a specified FaCS user
     *
     * @param $user_id
     *
     * @return array|null
     */
    function get( $user_id ) {

        $user_rec = null;

        // Query the user from the database
        $query = "SELECT * " .
                   "FROM " . $this->user_table  . " " .
                  "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $user_id ) . "'";
        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $user_rec = $this->gCI->db->fetch_assoc( $result );
            $user_rec = $this->strip_prefix( $user_rec );
        }

        return( $user_rec );
    }


    /**
     * get all data for admin
     */
    function get_by_email( $email ) {

        $user_rec = null;
        $query = "SELECT * " .
                   "FROM " . $this->user_table . " " .
                  "WHERE " . $this->query_field( 'email' ). " = '" . $this->gCI->db->escape_string( $email ) . "'";

        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $user_rec = $this->gCI->db->fetch_assoc( $result );
            $user_rec = $this->strip_prefix( $user_rec );
        }

        return( $user_rec );
    }


    /**
     * get all data for admin
     */
    function get_by_login( $email, $raw_password ) {

        $user_rec = null;
        $password = $this->password_hash( $email, $raw_password );
        $query = "SELECT * " .
                  "FROM " . $this->user_table . " " .
                 "WHERE " . $this->query_field( 'email' ) . " = '" . $this->gCI->db->escape_string( $email ) . "' " .
                   "AND " . $this->query_field( 'password' ) . " = '" . $this->gCI->db->escape_string( $password ) . "' ";

        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $user_rec = $this->gCI->db->fetch_assoc( $result );
            $user_rec = $this->strip_prefix( $user_rec );
        }

        return( $user_rec );
    }


    function update( $user_id, $update_fields ) {

        $set_part = '';
        foreach( $update_fields as $key => $value ) {
            if ( $this->field_is_in_dictionary( $key ) ) {
                if ( $key == 'password' ) {
                    $password = $value;
                    continue;
                }
                if ( $key == 'email' ) {
                    $email = $value;
                }
                if ( empty( $set_part ) ) {
                    $set_part = "SET ";
                } else {
                    $set_part .= ", ";
                }

                $set_part .= $this->query_field( $key ) . " = '" . $this->gCI->db->escape_string( $value ) ."' ";
            }
        }

        // Are they updating the password?
        if ( !empty( $password ) ) {
            if ( empty( $email ) ) {
                $user_rec = $this->get( $user_id );
                $email = $user_rec['email'];
            }
            $set_part .= ", " . $this->query_field( 'password' ) . " = '" . $this->gCI->db->escape_string( $this->password_hash( $email, $password ) ) .  "' ";
        }

        $return_val = null;
        if ( !empty( $set_part ) ) {

            // Update the base user table
            $query = "UPDATE " . $this->user_table . " " .
                            $set_part .
                     "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $user_id )  ."'";

            $return_val = $this->gCI->db->query( $query );
        }
        return( $return_val );
    }


    function deactivate( $user_id, $deactivate = true ) {
        $active_code = ( $deactivate ? '0' : '1' );
        return( $this->update( $user_id, array( 'active' => $active_code ) ) );
    }


    /**
     * Convert a password string to a hash
     *
     * @param $user_email
     * @param $raw_password
     *
     * @return string
     */
    function password_hash( $user_email, $raw_password ) {

        return(
            md5(
                PW_SALT . $raw_password . PW_WHEAT . $user_email . PW_PEPPER
            )
        );
    }


    /**
     * Replace a user's password with a new randomly generated one.
     *
     * @param $email
     *
     * @return string - the new password
     */
    function reset_password( $email ) {

        $user_rec = $this->get_by_email( $email );
        if ( !empty( $user_rec ) ) {
            $reset_string = substr( md5( rand( 1, 10000 ) ), 0, 16 );
            $this->update( $user_rec['id'], array( 'password' => $this->password_hash( $email, $reset_string ) ) );
        } else {
            $reset_string = null;
        }

        return( $reset_string );
    }

    /**
     * Build a query for fetching a list of admins
     *
     * @param $search_args - associative array with pageno, items_per_page, order_by, order_dir, security_min
     * @param bool $do_count - true to limit results of just a count of records matching query, not including paging
     * @param bool $do_limit - true to limit results (unless we're just doing a count)
     *
     * @return string
     */
    private function build_list_query( $search_args, $do_count = false, $do_limit = true ) {

        // Set up the limit clause based on the page number and items per page (for select only)
        if ( $do_limit && !$do_count )
            $limit_clause = " LIMIT " . ( ( $search_args['pageno'] - 1 ) * $search_args['items_per_page'] ) . ", " . $search_args['items_per_page'];
        else
            $limit_clause = "";

        // Did they specify a minimum security level?
        $where_clause = "WHERE 1 ";
        if ( !empty( $search_args['privilege_level'] ) ) {
            $where_clause .= "AND ( ( " . $this->query_field( 'privilege') . " & " . $this->gCI->db->escape_string( $search_args['privilege_level'] ) . " ) != 0 ) ";
        }
        if ( isset( $search_args['txt_keyword'] ) && !empty( $search_args['txt_keyword'] ) ) {
            $where_clause .=
                " AND ( " .
                      "( " . $this->query_field( 'name' ) . " LIKE '%" . $this->gCI->db->escape_string( $search_args['txt_keyword'] ) . "%' ) " .
                   "OR ( " . $this->query_field( 'email' ) . " LIKE '%" . $this->gCI->db->escape_string( $search_args['txt_keyword'] ) . "%' ) " .
                      ") ";

        }
        if ( isset( $search_args['active_filter'] ) && ( $search_args['active_filter'] != -1 ) ) {
            $where_clause .= " AND ( " . $this->query_field( 'active' ) . "='" . $this->gCI->db->escape_string( $search_args['active_filter'] ) . "' ) ";
        }
        if ( !empty( $search_args['active_only'] ) ) {
            $where_clause .= " AND ( " . $this->query_field( 'active' ) . "='1' ) ";
        }

        // Build the query string...
        $query = "SELECT ";
        if ( $do_count ) {
            $query .= "COUNT(*) as user_count ";
        } else {
            $query .= "* ";
        }
        $query .= "FROM " . $this->user_table . " ";
        $query .= $where_clause;

        if ( !$do_count ) {
            $query .= "ORDER BY " . $this->query_field( $search_args['order_by'] ) . " " . $search_args['order_dir'] . " " .
                $limit_clause;
        }

        return( $query );
    }


    /**
     * Get the list of admins as specified by the search arguments
     *
     * @param $search_args - associative array(
     *      'privilege_level' => USER_PRIV_ENDUSER,
     *      'txt_keyword'     => "user name",
     *      'active_filter'   => ACTIVE_FILTER_ALL,
     *      'active_only'     => TRUE (for active users only),
     *      'search_name'     => '',
     *      'order_by'        => 'name',
     *      'order_dir'       => 'ASC'
     * )
     * @param bool $do_limit
     *
     * @return array - the list of shops as an array of associative arrays
     */
    function get_list( $search_args, $do_limit = true  ) {

        $query = $this->build_list_query( $search_args, false, $do_limit );

        // If we have results, read them into the store_list
        $result = $this->gCI->db->query( $query );
        $user_list = array();
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            while ( $user_row = $this->gCI->db->fetch_assoc( $result ) ) {
                $user_row = $this->strip_prefix( $user_row );
                $user_list[] = $user_row;
            }
        }

        // Return the store list.
        return $user_list;
    }


    /**
     * Get a count of shops as specified by the search arguments provided by the user
     *
     * @param $search_args
     *
     * @return array - the list of shops as an array of associative arrays
     */
    function get_count( $search_args ) {

        $query = $this->build_list_query( $search_args, true );

        // If we have results, read them into the store_list
        $result = $this->gCI->db->query( $query );
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            if ( $user_row = $this->gCI->db->fetch_assoc( $result ) )
                return( $user_row['user_count'] );
        }

        // bad query
        return( 0 );
    }

    function list_priv( $privilege ) {
        $return_string = '';
        foreach( $this->priv_names as $priv_code => $priv_name ) {
            if ( ( $privilege & $priv_code ) != 0 ) {
                if ( !empty( $return_string ) ) {
                    $return_string .= ", ";
                }

                $return_string .= $priv_name;
            }
        }

        return( $return_string );
    }
}
