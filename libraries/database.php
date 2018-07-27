<?php
/**
 * Wrapper for the database functions. Currently based on PHP's mysqli
 */
class Database {

    public $db_conn;
    public $gCI;
    public $db_error = '';
    public $db_errno = 0;
    public $last_result = 0;
    public $last_query;

    private $hostname = null, $username = null, $password = null, $database = null, $port = 0;

    function __construct( $hostname = null, $username = null, $password = null, $port = 0, $database_name = null ) {

        if ( !empty( $hostname ) ) {
            $this->hostname = $hostname;
        }
        if ( !empty( $username ) ) {
            $this->username = $username;
        }
        if ( !empty( $password ) ) {
            $this->password = $password;
        }
        if ( !empty( $port ) ) {
            $this->port = $port;
        }
        if ( !empty( $database ) ) {
            $this->database = $database;
        }
    }

    function init() {

        // Connect to a database
        if ( empty( $this->hostname ) ) {
            $this->hostname = $this->gCI->gPZ['db_config']["hostname"];
        }
        if ( empty( $this->username ) ) {
            $this->username = $this->gCI->gPZ['db_config']["username"];
        }
        if ( empty( $this->password ) ) {
            $this->password = $this->gCI->gPZ['db_config']["password"];
        }
        if ( strstr( $this->hostname, ":" ) ) {
            list( $this->hostname, $this->port ) = explode( ":", $this->gCI->gPZ['db_config']['hostname'] );
        } else {
            $this->port = 0;
        }
        $this->database = ( empty( $this->database ) ? $this->gCI->gPZ['db_config']["database"] : $this->database );
        $this->db_conn = new mysqli( $this->hostname, $this->username, $this->password, $this->database, $this->port );

        if ( mysqli_connect_errno() ) {

            $this->gCI->fatal_error( "Database Connect failed: %s\n", mysqli_connect_error()) ;
        }
    }

    function escape_string( $string ) {

        return( $this->db_conn->escape_string( $string ) );
    }

    function query( $query_string ) {
        $this->last_query  = $query_string;
        $this->last_result = $this->db_conn->query( $query_string );
        return( $this->last_result );
    }

    function insert_id( ) {
        return( $this->db_conn->insert_id );
    }

    function errno() {
        return ( $this->db_conn->errno );
    }

    function error() {
        return ( $this->db_conn->error );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function fetch_assoc( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        return( empty( $result ) ? null : $result->fetch_assoc(  ) );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function fetch_array( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }
        return( empty( $result ) ? null : $result->fetch_array( ) );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function fetch_row( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }
        return( empty( $result ) ? null : $result->fetch_row( ) );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function fetch_object( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }
        return( empty( $result ) ? null : $result->fetch_object( ) );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function num_rows( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }
        return( empty( $result ) ? 0 : $result->num_rows );
    }



    function info( ) {
        return ( $this->db_conn->get_client_info( ) );
    }

    function affected_rows( ) {
        return( $this->db_conn->affected_rows  );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function free_result( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }
        $this->last_result = null;
        return( empty( $result ) ? null : $result->free( ) );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function data_seek( $row_number, $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        return( empty( $result ) ? null : $result->data_seek( $row_number ) );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function num_fields( $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        return( empty( $result ) ? 0 : $result->field_count );
    }

    function list_fields( $table_name ) {
        $query = "SHOW COLUMNS FROM table LIKE '" . $this->escape_string( $table_name ) ."' ";
        $this->last_result = $this->query( $query );

        return( $this->last_result );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function field_table( $field_offset, $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        $field_info = $result->fetch_field_direct( $field_offset );

        return( $field_info->table );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function field_name( $field_offset, $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        $field_info = $result->fetch_field_direct( $field_offset );

        return( $field_info->name );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function field_type( $field_offset, $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        $field_info = $result->fetch_field_direct( $field_offset );
        return( $field_info->type );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function field_len( $field_offset, $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        $field_info = $result->fetch_field_direct( $field_offset );
        return( $field_info->length );
    }

    /**
     * @param mysqli_result $result
     * @return mixed
     */
    function field_flags( $field_offset, $result = null ) {
        if ( empty( $result ) ) {
            $result = $this->last_result;
        }

        $field_info = $result->fetch_field_direct( $field_offset );
        return( $field_info->length );
    }
}
