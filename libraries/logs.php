<?php
/**
 * Writing and reading to a basic text log that can be viewed by administrators in the backend
 */
define( "LOG_BLOCK_OPEN",  "[![--" );
define( "LOG_BLOCK_CLOSE", "--]!]" );
define( "LOG_DATA_OPEN",   "[[--" );
define( "LOG_DATA_CLOSE",  "--]]" );

define( 'LOG_LOGINS',  'login_attempts' );
define( 'LOG_GENERAL', 'general' );


class Logs {

    public $gCI;

    private $logname = LOG_GENERAL;

    private $echo_msg = false;
    private $ended = false;
    public  $log_started = false;
    private $loglines = null;
    private $write_continuous = true;

    private $log_path = '';

    public $log_directory = array(
        LOG_LOGINS  => "Login Attempts",
        LOG_GENERAL => "General"
    );

    function __construct(  ) {
    }

    function init( ) {
        $this->log_path = $this->gCI->gPZ['uploads_path'] . 'logs/';
    }



    /**
     * Provided primarily for backwards comparability with legacy functions. Call this to establish or change the log
     * that is being written to. It is preferable to establish the log in the constructor.
     *
     * @param $logname - the name of the log being written to (as defined above)
     */
    function set_logname( $logname ) {
        $this->logname  = $logname;
    }

    /**
     * Provided primarily for backwards comparability with legacy functions. Call this to change the log's echo
     * setting. It is preferable to set this in the constructor.
     *
     * @param $echo_msg
     */
    function set_echo( $echo_msg ) {
        $this->echo_msg = $echo_msg;
    }

    function get_file_path( $date_sting, $logname = '' ) {
        $logname = !empty( $logname ) ? $logname : $this->logname;
        return( $this->log_path . $logname . '.' . $date_sting . '.txt' );
    }

    private function write_loglines( ) {
        if ( !empty( $this->loglines ) && ( is_array( $this->loglines ) ) && ( count( $this->loglines ) > 0 ) ) {
            $full_log_name = $this->get_file_path( date('Ymd' ) );
            $fp = fopen( $full_log_name, 'a' );
            foreach( $this->loglines as $log_string ) {
                fwrite($fp, $log_string );
            }
            fclose($fp);
        }
    }

    /**
     * Write a message to the log, with optional data.
     *
     * @param $msg - the message to write to the file
     * @param string $data - optional data that will be viewed as hidden/expandable in the viewer
     */
    function message( $msg, $data = '' ) {

        // Build the log line message
        if ( !empty( $data ) ) {
            $msg .= " " . LOG_DATA_OPEN . $data . LOG_DATA_CLOSE;
        }
        $log_string = date("M d y, H:i:s") . " - " . $msg . "\n";

        // Put the message in the log lines array
        if ( empty( $this->loglines ) ) {
            $this->loglines = array();
        }
        $this->loglines[] = $log_string;

        // If we are writing the log continuously (rather than buffering and writing once ) ...
        if ( $this->write_continuous ) {
            // Write the logline to file
            $this->write_loglines();

            // remove the logline from the log line array
            array_pop( $this->loglines );
        }

        // If we're echoing on screen, echo the message on screen
        if ( $this->echo_msg ) {
            ob_flush();
            print( $msg . "<br />" );
        }
    }

    /**
     * Provided primarily for backwards comparability with legacy functions. Write the first message to the log. It is
     * preferable to do this in the constructor
     *
     * @param $msg
     */
    function start( $msg ) {
        $this->log_started = true;
        $this->message( $msg . LOG_BLOCK_OPEN );
    }

    /**
     * Write the last message and close out the logging session.
     *
     * @param $msg
     */
    function end( $msg ) {
        $this->message( $msg . LOG_BLOCK_CLOSE );
        if ( !$this->write_continuous ) {
            $this->write_loglines();
        }
        $this->ended = true;
    }

    /**
     * Set the log to buffer lines and write them all out in one block. (Default behaviour is to write each log line
     * out at the time it is requested).
     */
    function buffer_log( ) {
        $this->write_continuous = false;
    }


    /**
     * Delete log files that are six months old.
     */
    function delete_old( ) {
        // Calculate the threshold date
        $six_months_ago = date( "Ymd", time() - ( ( 60 * 60 * 24 * 30 * 4 ) ) );

        // Create the threshold name based for this log type and the threshold date
        $thresh_name =  $this->logname . "." .  $six_months_ago . ".txt";

        foreach( $this->log_directory as $logname => $log_label ) {
            // Go through the directory of log files for this type.
            $log_file_spec = $this->get_file_path( "*", $logname );

            foreach ( glob( $log_file_spec ) as $filename ) {

                // If the current file is less than the threshold date
                $this_filename = basename( $filename );
                if ( $this_filename < $thresh_name ) {

                    // delete it.
                    $del_file_name =  $this->log_path . $this_filename;
                    unlink( $del_file_name );
                }
            }
        }
    }


    function get_list( $log_name = '' ) {

        $return_list = array( );
        $log_file_spec = $this->get_file_path( "*", $log_name );


        foreach ( glob( $log_file_spec ) as $filename ) {

            $name_parts = explode( ".", $filename );
            $extension  = array_pop( $name_parts );
            $date_part  = array_pop( $name_parts );
            $return_list[] = $date_part;
        }

        arsort( $return_list );
        return( $return_list );
    }
}