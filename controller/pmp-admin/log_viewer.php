<?php
/**
 *
 * Main controller for all Administrative backend pages
 *
 */

class Log_viewer extends My_Controller {


    function __construct( $request ) {
        parent::__construct( $request );

        $this->library( "logs" );
    }


    // ----------------------- LOG VIEW --------------------------------------------------------------------------------
    public function index( $action = 'display', $log_type = LOG_GENERAL, $date = '' ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/view_logs" ) ) {

            $this->logs->delete_old( );

            $this->logs->set_logname( $log_type );
            $log_list     = $this->logs->get_list( );
            $log_contents = '';

            if ( empty( $date ) )
                $date = current( $log_list );
            $this->set_message( "Please select a date to view", 'info' );

            if ( !empty( $date ) ) {

                if ( $action == 'download' ) {
                    header( 'Content-Type: text/plain' );
                    readfile( $this->logs->get_file_path( $date ) );
                    die();
                } else {
                    $log_file_name =  $this->logs->get_file_path( $date );

                    if ( file_exists( $log_file_name ) ) {
                        ini_set('memory_limit', '-1');
                        $log_contents = file_get_contents( $log_file_name );
                        $log_contents = nl2br( str_replace( " ", "&nbsp;", htmlentities( $log_contents ) ) );
                        $log_contents = str_replace( LOG_BLOCK_OPEN, "<div class='log-block log-block-closed'><div class='log-block-toggle btn btn-mini'><span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span></div><div class='log-block-body'>", $log_contents );
                        $log_contents = str_replace( LOG_BLOCK_CLOSE, "</div></div>", $log_contents );
                        $log_contents = str_replace( LOG_DATA_OPEN, "<div class='log-data log-data-closed'><div class='log-data-toggle'>+</div><div class='log-data-block'>", $log_contents );
                        $log_contents = str_replace( LOG_DATA_CLOSE, "</div></div>", $log_contents );
                        $this->set_message( "Log loaded for " . $this->log_fmt_date( $date ), 'success' );
                    } else {
                        $this->set_message( "<strong>ERROR!</strong> There is no log for " . $this->log_fmt_date( $date ) . "!", 'error' );
                    }
                }
            }

            $this->data( 'log_types',    $this->logs->log_directory );
            $this->data( 'log_type',     $log_type );
            $this->data( 'log_list',     $log_list );
            $this->data( 'log_contents', $log_contents );

            $this->data( 'date',         $date );

            $this->data( 'template', 'log_viewer.php' );

            $this->asset_request( REQ_ASSET_CSS, 'admin_logs' );
            $this->asset_request( REQ_ASSET_JS,  'admin_logs' );

            return( $this->view( ) );
        } else {
            return( '' );
        }
    }

    private function log_fmt_date( $date ) {
        return( substr( $date, 4, 2 ) . "-" . substr( $date, 6, 2 ) . "-" . substr( $date, 0, 4 ) );
    }
}