<?php

/**
 * Controller for word.coffee session saving
 */

class Download extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

        $this->library( 'convert_html' );
    }


    public function index( ) {

        $this->set_mode( MODE_BARE );

        $sample_text = $this->get_var( 'sample_text', '' );
        $type        = $this->get_var( 'type',        CONVERT_TYPE_RTF );


        $this->convert_html->set_type( $type );

        $this->data( 'output_text', $this->convert_html->convert( $sample_text ) );
        $this->data( 'mime_type',   $this->convert_html->get_mime( ) );
        $this->data( 'file_name',   $this->convert_html->get_filename( ) );


        return( $this->view( 'bare_download.php' ) );
    }

}
