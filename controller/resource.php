<?php
/**
 * Dynamic image and thumbnail service
 */
class Resource extends My_Controller {

    // Return the code as a file to the browser
    private $mime_types = null;

    function __construct( $request ) {
        parent::__construct( $request );

        $this->library( "codify" );

        $this->mime_types = array(
            "css"   => "text/css",
            "eot"   => "application/vnd.ms-fontobject",
            "gif"   => "image/gif",
            "jpeg"  => "mage/jpeg",
            "jpg"   => "mage/jpeg",
            "js"    => "application/javascript",
            "png"   => "image/png",
            "svg"   => "image/svg+xml",
            "ttf"   => "application/x-font-ttf",
            "woff"  => "application/x-font-woff",
            "woff2" => "application/x-font-woff",
        );
    }


    /**
     * Serve the specified image to the browser as image content.
     *
     * NOTE: calling this function will terminate the script (die).
     *
     * @param $file_path
     * @param $file_mime
     */
    function serve_file( $file_path, $file_mime ) {

        ob_end_clean();
        header( 'Content-Type: ' . $file_mime );
        readfile( $file_path );

        die();
    }




    function index( $code_type = '', $load_file = '' )  {

        // If the file name is empty, load a 404 page
        if ( empty( $load_file ) || empty( $code_type ) ) {
            header("Location: " . $this->gPZ['base_url'], TRUE, 404 );
            die();
        } else {

            // Get the path for the code
            $code_location = $this->codify->find_path( $code_type ) . $load_file;

            $this->serve_file( $code_location,  $this->mime_types[ $code_type] );
        }
    }

    function fonts( $load_file = '' ) {

        // If the file name is empty, load a 404 page
        if ( empty( $load_file ) ) {
            header("Location: " . $this->gPZ['base_url'], TRUE, 404 );
            die();
        } else {

            // Get the type of code from the file name extension
            $file_parts = explode( ".", $load_file );
            $font_type  = $file_parts[2];

            // Get the path for the code
            $code_location = $this->gPZ['doc_root'] . "assets/fonts/" . $load_file;

            $this->serve_file( $code_location,  $this->mime_types[$font_type] );
        }
    }

    function img( $load_file = '' ) {

        // If the file name is empty, load a 404 page
        if ( empty( $load_file ) ) {
            header("Location: " . $this->gPZ['base_url'], TRUE, 404 );
            die();
        } else {

            // Get the type of code from the file name extension
            $file_parts = explode( ".", $load_file );
            $image_type  = $file_parts[2];

            // Get the path for the code
            $code_location = $this->gPZ['doc_root'] . "assets/img/" . $load_file;

            $this->serve_file( $code_location,  $this->mime_types[$image_type] );
        }
    }

}
