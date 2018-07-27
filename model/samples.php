<?php

/**
 * Model for Writing Samples
 */


class Samples extends PZ_Model {

    private $samples_table    = 'samples';
    private $field_prefix     = 'sample_';
    private $field_dictionary = array(
        'id'                => 0,
        'user_id'           => 0,
        'session_id'        => 0,
        'prompt_id'         => 0,
        'text'              => '',
        'date_added'        => BLANK_DATE,
        'date_updated'      => BLANK_DATE,
        'public'            => 0
    );


    function __construct(  ) {
        parent::__construct(  );

    }

    private function field_is_in_dictionary( $field_name ) {
        return( isset( $this->field_dictionary[$field_name] ) );
    }

    private function strip_prefix( $sample_rec ) {
        $new_user_rec = array();
        foreach( $sample_rec as $key => $value ) {
            $stripped_key = str_replace( $this->field_prefix, '', $key );
            if ( $this->field_is_in_dictionary( $stripped_key ) ) {
                $new_user_rec[$stripped_key] = $value;
            }
        }

        return( $new_user_rec );
    }

    private function query_field( $field_suffix ) {
        return( $this->samples_table . "." . $this->field_prefix . $field_suffix );
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

                if ( ( $key == 'id' ) || ( $key == 'date_added' ) || ( $key == 'date_updated' ) ) {
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

            $set_part .= ", " . $this->query_field( 'date_added' ) . " = now()";
            $set_part .= ", " . $this->query_field( 'date_updated' ) . " = now()";


            // Now add the corresponding CSL Admin record...
            $query = "INSERT INTO " . $this->samples_table . " " .
                        $set_part;

            $this->gCI->db->query( $query );
            return ( $this->gCI->db->insert_id() );
        }
    }


    /**
     * Get the data for a specified writing sample
     *
     * @param $sample_id
     *
     * @return array|null
     */
    function get( $sample_id ) {

        $sample_rec = null;

        // Query the user from the database
        $query = "SELECT * " .
                   "FROM " . $this->samples_table . " " .
                  "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $sample_id ) . "'";
        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $sample_rec = $this->gCI->db->fetch_assoc( $result );
            $sample_rec = $this->strip_prefix( $sample_rec );
        }

        return( $sample_rec );
    }


    /**
     * get all data for admin
     */
    function get_for_session( $session_id, $prompt_id = 0 ) {

        $where_part = "WHERE " . $this->query_field( 'session_id' ) . " = '" . $this->gCI->db->escape_string( $session_id ) . "' ";
        if ( !empty( $prompt_id ) ) {
            $where_part .= "AND " . $this->query_field( 'prompt_id' ) . " = '" . $this->gCI->db->escape_string( $prompt_id ) . "' ";
        }

        $sample_rec = null;
        $select_part = "SELECT * FROM " . $this->samples_table . " ";
        $limit_part = "LIMIT 1";
        $query =  $select_part .
                  $where_part .
                  $limit_part;

        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $sample_rec = $this->gCI->db->fetch_assoc( $result );
            $sample_rec = $this->strip_prefix( $sample_rec );
        }

        return( $sample_rec );
    }

    /**
     * Update a writing prompt to the database
     * @param $sample_id
     * @param $update_fields
     * @return null
     */
    function update( $sample_id, $update_fields ) {

        $set_part = '';
        foreach( $update_fields as $key => $value ) {
            if ( $this->field_is_in_dictionary( $key ) ) {
                if ( ( $key == 'id' ) || ( $key == 'date_added' ) || ( $key == 'date_updated' ) ) {
                    continue;
                }
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
            $set_part .= ", " . $this->query_field( 'date_updated' ) . " = now()";

            // Update the base user table
            $query = "UPDATE " . $this->samples_table . " " .
                            $set_part .
                     "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $sample_id )  ."'";

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
    function delete( $sample_id, $delete = true ) {
        $delete_code = ( $delete ? '1' : '0' );
        return( $this->update( $sample_id, array( 'deleted' => $delete_code ) ) );
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
        $query .= "FROM " . $this->samples_table . " ";
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
