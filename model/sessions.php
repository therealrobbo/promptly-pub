<?php

/**
 * Model for user Sessions
 */

define( 'SESSION_SALT',   "SupermanBatmanWolverinePulpFiction" );
define( 'SESSION_PEPPER', "WhoaJimPaleMoonlightWheelsThoughIWalk" );


class Sessions extends PZ_Model {

    private $sessions_table   = 'sessions';
    private $field_prefix     = 'session_';
    private $field_dictionary = array(
        'id'                => 0,
        'hash'              => '',
        'ip'                => '',
        'date_created'      => BLANK_DATE
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
        return( $this->sessions_table . "." . $this->field_prefix . $field_suffix );
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

                if ( ( $key == 'id' ) || ( $key == 'date_created'  ) ) {
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

            $set_part .= ", " . $this->query_field( 'date_created' ) . " = now()";


            // Now add the corresponding CSL Admin record...
            $query = "INSERT INTO " . $this->sessions_table . " " .
                        $set_part;

            $this->gCI->db->query( $query );
            return ( $this->gCI->db->insert_id() );
        }
    }


    /**
     * Get the data for a specified session
     *
     * @param $session_id
     *
     * @return array|null
     */
    function get( $session_id ) {

        $sample_rec = null;

        // Query the user from the database
        $query = "SELECT * " .
                   "FROM " . $this->sessions_table . " " .
                  "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $session_id ) . "'";
        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $sample_rec = $this->gCI->db->fetch_assoc( $result );
            $sample_rec = $this->strip_prefix( $sample_rec );
        }

        return( $sample_rec );
    }

    /**
     * Get the data for a specified session
     *
     * @param $session_hash
     *
     * @return array|null
     */
    function get_by_hash( $session_hash ) {

        $sample_rec = null;

        // Query the user from the database
        $query = "SELECT * " .
            "FROM " . $this->sessions_table . " " .
            "WHERE " . $this->query_field( 'hash' ) . " = '" . $this->gCI->db->escape_string( $session_hash ) . "'";
        $result = $this->gCI->db->query($query);
        if ( !empty( $result ) && ( $this->gCI->db->num_rows( $result ) != "0" ) ) {

            $sample_rec = $this->gCI->db->fetch_assoc( $result );
            $sample_rec = $this->strip_prefix( $sample_rec );
        }

        return( $sample_rec );
    }

    /**
     * Update a writing prompt to the database
     * @param $session_id
     * @param $update_fields
     * @return null
     */
    function update( $session_id, $update_fields ) {

        $set_part = '';
        foreach( $update_fields as $key => $value ) {
            if ( ( $key == 'id' ) || ( $key == 'date_created'  ) ) {
                continue;
            }
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
            $query = "UPDATE " . $this->sessions_table . " " .
                            $set_part .
                     "WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $session_id )  ."'";

            $return_val = $this->gCI->db->query( $query );
        }
        return( $return_val );
    }


    /**
     * Delete (or undelete) a writing prompt
     *
     * @param $prompt-id
     *
     * @return null
     */
    function delete( $session_id ) {
        $query = "DELETE FROM " . $this->sessions_table . " WHERE " . $this->query_field( 'id' ) . " = '" . $this->gCI->db->escape_string( $session_id ) . "' ";
        return( $this->gCI->db->query( $query ) );
    }


    /**
     * Make a Hash string for the current user session
     *
     * @param $session_id
     *
     * @return string
     */
    private function make_hash( $session_id ) {
        return( md5( SESSION_SALT . $this->fetch_ip_string() . SESSION_PEPPER . $session_id ) );
    }

    private function fetch_cookie( ) {

        $return_cookie = null;

        // Is there a login cookie already?
        if ( isset( $_COOKIE[$this->gCI->gPZ['session_cookie']] ) ) {
            $return_cookie = $_COOKIE[$this->gCI->gPZ['session_cookie']];
        }

        return( $return_cookie );
    }

    private function set_cookie( $session_id, $delete = false ) {

        setcookie(
            $this->gCI->gPZ['session_cookie'],
            ( !$delete ? $this->make_hash( $session_id ) : '' ),
            ( !$delete ? time()+31536000 : time()-86400 ),
            "/"
        );
    }

    private function fetch_ip_string( ) {
        return( $_SERVER['REMOTE_ADDR'] . "||" .  $_SERVER['HTTP_X_FORWARDED_FOR'] );
    }

    public function get_current( ) {

        $session_id = 0;

        // Do they have a session cookie?
        $sesson_hash = $this->fetch_cookie();
        if ( !empty( $sesson_hash ) ) {

            // They do have a session cookie. Get the corresponding session record
            $session_rec = $this->get_by_hash( $sesson_hash );
            if ( !empty( $session_rec ) ) {

                // There is a session record for this user.
                // Now verify that it comes from the same IP address
                if ( $session_rec['ip'] == $this->fetch_ip_string() ) {

                    // The IP strings match, so send back the session ID
                    $session_id = $session_rec['id'];
                }
            }
        }

        return( $session_id );
    }


    public function start_session( ) {

        $session_id = $this->add(
            array(
                'hash'              => '',
                'ip'                => $this->fetch_ip_string()
            )
        );

        $this->update( $session_id, array( 'hash' => $this->make_hash( $session_id ) ) ) ;

        $this->set_cookie( $session_id );
    }


}
