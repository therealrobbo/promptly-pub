<?php

/**
 * Model for Writing Prompts
 */

define( 'PS_TRASHED_ALL', -1 );
define( 'PS_TRASHED_YES', '1' );
define( 'PS_TRASHED_NO',  '0' );



class Prompts extends PZ_Model {

    private $prompt_table     = 'prompts';
    private $field_prefix     = 'prompt_';
    private $max_use_date     = null;
    private $field_dictionary = array(
        'id'                => 0,
        'text'              => '',
        'date_added'        => BLANK_DATE,
        'date_updated'      => BLANK_DATE,
        'use_date'          => BLANK_DATE,
        'deleted'           => 0
    );


    function __construct(  ) {
        parent::__construct(  );

    }

    private function field_is_in_dictionary( $field_name ) {
        return( isset( $this->field_dictionary[$field_name] ) );
    }

    private function strip_prefix( $prompt_rec ) {
        $new_user_rec = array();
        foreach( $prompt_rec as $key => $value ) {
            $stripped_key = str_replace( $this->field_prefix, '', $key );
            if ( $this->field_is_in_dictionary( $stripped_key ) ) {
                $new_user_rec[$stripped_key] = $value;
            }
        }

        return( $new_user_rec );
    }

    private function query_field( $field_suffix ) {
        return( $this->prompt_table . "." . $this->field_prefix . $field_suffix );
    }

    function dummy( ) {
        $return_rec = array( );
        foreach( $this->field_dictionary  as $key => $value ) {
            $return_rec[$key] = $value;
        }

        return( $return_rec );
    }

    /**
     * Add a new prompt to the database
     *
     * @param null $update_fields
     * @return int
     */
    function add( $update_fields = null ) {

        $set_part = '';
        foreach( $update_fields as $key => $value ) {
            if ( $this->field_is_in_dictionary( $key ) ) {

                if ( $key == 'id' ) {
                    continue;
                }
                if ( empty( $set_part ) ) {
                    $set_part = "SET ";
                } else {
                    $set_part .= ", ";
                }

                $set_part .= $this->query_field( $key ) . " = '" . $this->gCI->db->escape_string( $value ) .  "' ";
            }
        }

        if ( empty( $set_part ) ) {
            return ( 0 );
        } else {

            if ( empty( $this->max_use_date ) ) {
                $this->max_use_date = BLANK_DATE;
                $today = date( "Y-m-d" ) . " 00:00:01 ";
                $recent_rec = $this->get_most_recently_used();
                if ( !empty( $recent_rec ) ) {
                    $this->max_use_date = $recent_rec['use_date'];
                    if ( $this->max_use_date < $today ) {
                        $this->max_use_date = $today;
                    }
                }
            }
            $this->max_use_date = date( "Y-m-d", strtotime( $this->max_use_date ) + ( 60 * 60 * 24 ) ) . " 00:00:01";
            $set_part .= ", " . $this->query_field( 'use_date' ) . " = '" . $this->max_use_date . "' ";

            $set_part .= ", " . $this->query_field( 'date_added' ) . " = now()";
            $set_part .= ", " . $this->query_field( 'date_updated' ) . " = now()";


            // Now add the corresponding CSL Admin record...
            $query = "INSERT INTO " . $this->prompt_table . " " .
                        $set_part;

            $this->gCI->db->query( $query );
            return ( $this->gCI->db->insert_id() );
        }
    }


    /**
     * Get the data for a specified FaCS user
     *
     * @param $prompt_id
     *
     * @return array|null
     */
    function get( $prompt_id ) {

        $prompt_rec = null;

        // Query the user from the database
        $query = "SELECT * " .
                   "FROM " . $this->prompt_table . " " .
                  "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $prompt_id ) . "'";
        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $prompt_rec = $this->gCI->db->fetch_assoc( $result );
            $prompt_rec = $this->strip_prefix( $prompt_rec );
        }

        return( $prompt_rec );
    }


    /**
     * get all data for admin
     */
    function get_least_recently_used( $cutoff_date = '' ) {

        $where_part = "WHERE " . $this->query_field( 'deleted' ) . "=0 ";
        if ( !empty( $cutoff_date ) ) {
            $where_part .= "AND " . $this->query_field( 'use_date' ) . " > '" . $this->gCI->db->escape_string( $cutoff_date ) . "' ";
        }

        $prompt_rec = null;
        $select_part = "SELECT * FROM " . $this->prompt_table . " ";
        $order_part  = "ORDER BY " . $this->query_field( 'use_date' ) . " ASC, " .
                                     $this->query_field( 'date_added' ) . " ASC, " .
                                     $this->query_field( 'date_updated' ) . " ASC ";
        $limit_part = "LIMIT 1";
        $query =  $select_part .
                  $where_part .
                  $order_part .
                  $limit_part;

        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $prompt_rec = $this->gCI->db->fetch_assoc( $result );
            $prompt_rec = $this->strip_prefix( $prompt_rec );
        } else {
            // Try again without the cutoff date
            $query =  $select_part .
                      $order_part .
                      $limit_part;
            $result = $this->gCI->db->query($query);
            if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

                $prompt_rec = $this->gCI->db->fetch_assoc( $result );
                $prompt_rec = $this->strip_prefix( $prompt_rec );
            }
        }

        return( $prompt_rec );
    }

    /**
     * get all data for admin
     */
    function get_today( ) {

        $prompt_rec = null;
        $query = "SELECT * " .
                   "FROM " . $this->prompt_table . " " .
                  "WHERE " . $this->query_field( 'deleted' ) . "=0 " .
               "ORDER BY " . $this->query_field( 'use_date' ) . " ASC, " .
                             $this->query_field( 'date_added' ) . " ASC, " .
                             $this->query_field( 'date_updated' ) . " ASC " .
            "LIMIT 1";

        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $prompt_rec = $this->gCI->db->fetch_assoc( $result );
            $prompt_rec = $this->strip_prefix( $prompt_rec );
        }

        return( $prompt_rec );
    }

    function select_for_today(  ) {

        // Get the prompt that has been used lease recently
        $prompt_rec = $this->get_least_recently_used( date( "Y-m-d", time( ) - ( 60 * 60 * 24 ) ) . " 23:59:59" );
        if ( !empty( $prompt_rec ) ) {

            // We found a record. Was is scheduled for today?
            $use_date = substr( $prompt_rec['use_date'], 0, 8 );
            $today    = date( "Y-m-d" );
            if ( $use_date != $today ) {

                // It was not scheduled for today, so lets mark it as being used today
                $this->update( $prompt_rec['id'], array( 'use_date' => $today . " 00:00:00" ) );
            }
        }

        return( $prompt_rec );
    }

    /**
     * get all data for admin
     */
    function get_most_recently_used( ) {

        $prompt_rec = null;
        $query = "SELECT * " .
                   "FROM " . $this->prompt_table . " " .
               "ORDER BY " . $this->query_field( 'use_date' ) . " DESC, " .
                             $this->query_field( 'date_added' ) . " ASC, " .
                             $this->query_field( 'date_updated' ) . " ASC " .
                  "LIMIT 1";

        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $prompt_rec = $this->gCI->db->fetch_assoc( $result );
            $prompt_rec = $this->strip_prefix( $prompt_rec );
        }

        return( $prompt_rec );
    }


    /**
     * Update a writing prompt to the database
     * @param $prompt_id
     * @param $update_fields
     * @return null
     */
    function update( $prompt_id, $update_fields ) {

        $set_part = '';
        foreach( $update_fields as $key => $value ) {
            if ( $this->field_is_in_dictionary( $key ) ) {
                if ( empty( $set_part ) ) {
                    $set_part = "SET ";
                } else {
                    $set_part .= ", ";
                }

                $set_part .= $this->query_field( $key ) . " = '" . $this->gCI->db->escape_string( $value ) ."' ";
            }
        }

        $return_val = null;
        if ( !empty( $set_part ) ) {

            // Update the base user table
            $query = "UPDATE " . $this->prompt_table . " " .
                            $set_part .
                     "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $prompt_id )  ."'";

            $return_val = $this->gCI->db->query( $query );
        }
        return( $return_val );
    }


    /**
     * Delete (or undelete) a writing prompt
     *
     * @param $prompt-id
     * @param bool $delete
     *
     * @return null
     */
    function delete( $prompt_id, $delete = true ) {
        $delete_code = ( $delete ? '1' : '0' );
        return( $this->update( $prompt_id, array( 'deleted' => $delete_code ) ) );
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
        if ( isset( $search_args['txt_keyword'] ) && !empty( $search_args['txt_keyword'] ) ) {
            $where_clause .=
                " AND ( " . $this->query_field( 'text' ) . " LIKE '%" . $this->gCI->db->escape_string( $search_args['txt_keyword'] ) . "%' ) ";
        }
        if ( isset( $search_args['trash_filter'] ) && ( $search_args['trash_filter'] != PS_TRASHED_ALL ) ) {
            $where_clause .=
                " AND ( " . $this->query_field( 'deleted' ) . " = '" . $this->gCI->db->escape_string( $search_args['trash_filter'] ) . "' ) ";
        }

        // Build the query string...
        $query = "SELECT ";
        if ( $do_count ) {
            $query .= "COUNT(*) as prompt_count ";
        } else {
            $query .= "* ";
        }
        $query .= "FROM " . $this->prompt_table . " ";
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
     *      'txt_keyword'     => "user name",
     *      'trash_filter'    => PS_TRASHED_NO,
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
                return( $user_row['prompt_count'] );
        }

        // bad query
        return( 0 );
    }
}
