<?php
/**
 *
 * Main controller for all Administrative backend pages
 *
 */


class Sysinfo extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

    }


    //----------------------- L A N D I N G / L O G I N ----------------------------------------------------------------

    public function index( ) {

        if ( $this->admin_auth( $this->gPZ['backend_url'] . "/sysinfo" ) ) {

            $page_title = 'System Information';
            $this->data( 'title',    $this->gen_page_title( $page_title ) );
            $this->data( 'heading',  $page_title );

            $this->data( 'template', 'sysinfo.php' );

            return( $this->view() );
        } else {
            return( '' );
        }
    }
}