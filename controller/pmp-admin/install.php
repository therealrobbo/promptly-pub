<?php
/**
 *
 * Main controller for all Administrative backend pages
 *
 */

class Install extends My_Controller {


    function __construct( $request ) {
        parent::__construct( $request );

    }


    // ----------------------- LOG VIEW --------------------------------------------------------------------------------
    public function index(  ) {

        $dirs = array(
            'code' => array(
                'css'   => array(
                    'min'   => 0
                ),
                'js'    => array(
                    'min'   => 0
                )
            ),
            'logs' => 0
        );

        echo "making " .  $this->gPZ['uploads_path'] . "...<br />";
        mkdir( $this->gPZ['uploads_path'], '0777' );
        $this->make_subdirs( $this->gPZ['uploads_path'], $dirs );
        die();
    }

    private  function make_subdirs( $base_dir, $dirs ) {
        foreach( $dirs as $dir_name => $subdirs ) {

            mkdir( $base_dir . $dir_name, '0777' );
            echo "making " .  $base_dir . $dir_name . "...<br />";
            if ( !empty( $subdirs ) ) {
                $this->make_subdirs( $base_dir . $dir_name . "/", $subdirs );
            }
        }
    }

}