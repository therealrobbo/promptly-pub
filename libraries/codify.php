<?php
/**
 * Code bundler / minifier for CSS and JS files
 *
 * USAGE
 *
 *       // Add JS files
 *       $this->codify->add_js( 'global_js' );
 *       $this->codify->add_js( 'homepage' );
 *
 *       // Add CSS files
 *       $this->codify->add_css( 'global_front' );
 *       $this->codify->add_css( 'home_page' );
 *
 *       // Get URLS to the bundled/minified code
 *       $js_url  = $this->codify->get_js( );
 *       $css_url = $this->codify->get_css(  );
 *
 */


require_once( 'minify/JSMinifier.php');

define( 'CODIFY_CODE_TYPE_CSS',  'css' );
define( 'CODIFY_CODE_TYPE_JS',   'js' );

define( 'CODIFY_TYPE_FILE',    0 );
define( 'CODIFY_TYPE_SNIPPET', 1 );


/**
 * Wrapper for the database.
 */

class Codify {

    public $gCI;
    public $blocks = array(
        CODIFY_CODE_TYPE_CSS    => array(),
        CODIFY_CODE_TYPE_JS     => array()
    );
    public $minify = false;
    public $use_bundler = true;
    public  $is_ssl = false;
    private $code_path, $code_js_path, $code_js_min_path, $code_css_path, $code_css_min_path;
    private $code_serve_path, $code_serve_path_css, $code_serve_path_js;
    private $module_prefix;
    private $asset_path, $asset_css_path, $asset_js_path;


    /**
     * Constructor
     *
     * @param $minify - TRUE to compress files, FALSE to leave uncompressed
     */
    function __construct( ) {
        $this->set_minify( );
        $this->set_prefix( );
    }

    function init( ) {

        $this->set_ssl( $this->gCI->gPZ['ssl_environment'] );
        $this->code_path         = $this->gCI->gPZ['uploads_path'] . 'code/';

        $this->code_js_path      = $this->code_path . 'js/';
        $this->code_js_min_path  = $this->code_js_path . 'min/';

        $this->code_css_path     = $this->code_path . 'css/';
        $this->code_css_min_path = $this->code_css_path . 'min/';

        $this->asset_path        = "assets/";
        $this->asset_js_path     = $this->asset_path .  "js/";
        $this->asset_css_path    = $this->asset_path .  "css/";

        $this->code_serve_path     = ( $this->is_ssl ? $this->gCI->gPZ['base_url_ssl'] : $this->gCI->gPZ['base_url'] ) . '/resource';
        $this->code_serve_path_css = $this->code_serve_path . '/css';
        $this->code_serve_path_js  = $this->code_serve_path . '/js';
    }

    public function set_minify( $minify = false ) {
        $this->minify = $minify;
    }
    public function set_prefix( $prefix = '' ) {
        $this->module_prefix = $prefix;
    }
    public function set_ssl( $is_ssl = false ) {
        $this->is_ssl = $is_ssl;
    }

    private function obj_in_list( $code_type, $info ) {
        foreach( $this->blocks[$code_type] as $code_obj ) {
            if ( $code_obj->info == $info ) {
                return( true );
            }
        }

        return( false );
    }


    /**
     * Generic object adder
     *
     * @param $code_type
     * @param $info
     */
    private function add_obj( $code_type, $info ) {

        if ( !$this->obj_in_list( $code_type, $info ) ) {
            $this->blocks[$code_type][] = new Code_obj( $info, $this->gCI->gPZ['doc_root']  );
        }
    }

    /**
     * Add a CSS file to the list of CSS files to include
     *
     * @param $info
     */
    function add_css( $info ) {
        $extension = ( strstr( $info, ".css") ? '' : '.css' );
        $this->add_obj( CODIFY_CODE_TYPE_CSS, $this->asset_css_path . $info . $extension );
    }


    /**
     * Add a JavaScript file to the list of JS files to include
     *
     * @param $info
     */
    function add_js( $info ) {
        $extension = ( strstr( $info, ".js") ? '' : '.js' );
        $this->add_obj( CODIFY_CODE_TYPE_JS, $this->asset_js_path . $info . $extension );
    }

    /**
     * Generic file getter
     *
     * @param $code_type
     * @return bool|string
     */
    private function get( $code_type ) {

        $name_str = "";
        $max_date = 0;
        foreach( $this->blocks[$code_type] as $code_obj ) {
            $name_str .= $code_obj->info;
            $max_date = max( $max_date, $code_obj->timestamp );
        }
        $hash_name = md5( $name_str );

        $cur_file = $this->find_module( $hash_name, $code_type, $max_date );

        if ( empty( $cur_file ) ) {
            $cur_file = $this->build( $this->blocks[$code_type], $hash_name, $code_type );
        }

        $base_path = ( ( $code_type == CODIFY_CODE_TYPE_CSS ) ? $this->code_serve_path_css : $this->code_serve_path_js );
        return( empty( $cur_file ) ? false : $base_path . "/" . $cur_file );
    }


    /**
     * Get the URL/name of the bundled/compressed CSS file
     *
     * @return bool|string
     */
    function get_css( ) {
        return( $this->get( CODIFY_CODE_TYPE_CSS ) ) ;
    }

    /**
     * Get the URL/name of the bundled/compressed JS file
     *
     * @return bool|string
     */
    function get_js( ) {
        return( $this->get( CODIFY_CODE_TYPE_JS ) ) ;
    }


    private function file_count( $code_type ) {
        return( count( $this->blocks[$code_type] ) );
    }


    function css_count( ) {
        return( $this->file_count( CODIFY_CODE_TYPE_CSS ) );
    }

    function js_count( ) {
        return( $this->file_count( CODIFY_CODE_TYPE_JS ) );
    }


    private function get_file_list( $code_type ) {

        $return_array = array();

        foreach( $this->blocks[$code_type] as $code_obj ) {
            $return_array[] = $code_obj->info;
        }

        return( $return_array );
    }

    public function get_css_list( ) {
        return( $this->get_file_list( CODIFY_CODE_TYPE_CSS ) );
    }
    public function get_js_list( ) {
        return( $this->get_file_list( CODIFY_CODE_TYPE_JS ) );
    }


    /**
     * Find the destination path of the object
     *
     * @param $code_type
     * @return string
     */
    function find_path( $code_type ) {
        return(
        ( $code_type == CODIFY_CODE_TYPE_JS ) ?
            ( ( $this->minify ) ? $this->code_js_min_path  : $this->code_js_path ) :
            ( ( $this->minify ) ? $this->code_css_min_path : $this->code_css_path )
        );
    }

    /**
     * Find the latest module name for the requested bundle
     *
     * @param $module_name
     * @param $code_type
     * @return bool|string
     */
    private function find_module( $module_name, $code_type, $stale_date = 0 ) {

        $search_res  = glob( $this->find_path( $code_type ) . $this->module_prefix . $module_name . ".*." . $code_type );
        arsort( $search_res );

        foreach ( $search_res as $filename ) {

            $return_name = basename( $filename );
            if ( !empty( $stale_date ) ) {
                $name_parts = explode( ".", $return_name );
                if ( $name_parts[1] >= date( "YmdHis", $stale_date ) ) {
                    return( $return_name );
                }
            } else {
                return( $return_name );
            }
        }

        return( false );

    }


    /**
     * Compress a block of CSS code
     *
     * @param $buffer
     * @return mixed
     */
    private function compress_css( $buffer ) {

        /* remove comments */
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array('    '), ' ', $buffer);
        $buffer = str_replace(array('   '), ' ', $buffer);
        $buffer = str_replace(array('  '), ' ', $buffer);
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);

        return $buffer;
    }


    /**
     * Bulid the bundled/compressed file
     *
     * @param $file_list
     * @param $module_name
     * @param $code_type
     * @return bool|string
     * @throws Exception
     */
    function build( $file_list, $module_name, $code_type ) {

        $source_path =  $this->find_path( $code_type );
        $mini_options = array( 'flaggedComments' => false );

        // Start the content buffer
        $file_buffer = $name_list = "";

        // Loop through the file list and add them to the content buffer.
        foreach( $file_list as $code_obj ) {

            // Try several times to get the file in case it is locked by another process...
            $max_retries   = 5;
            $file_contents = false;
            while ( ( $max_retries > 0 ) && ( $file_contents === false ) ) {
                $file_contents = file_get_contents( $this->gCI->gPZ['doc_root'] . $code_obj->info );
                $max_retries--;
            }
            $file_contents .= "\n\n";

            // If we're not minifying...
            if ( !$this->minify ) {

                // Add a comment above this file's contents
                $file_buffer .= "\n/* --------------------------------------------------------------------------------------------------------------------\n" .
                                " * \n" .
                                " * INCLUDE -- " . $code_obj->info . "\n" .
                                " * \n" .
                                " * ----------------------------------------------------------------------------------------------------------------------*/\n";
                $file_buffer .= $file_contents;

                // Add the file name to the list of file names in this bundle
                $name_list   .= $code_obj->info . "\n";
            } else {

                // We ARE minifying so put some blank space in between file blobs
                $file_buffer .= "\n\n\n";

                // Minify according to type
                if ( $code_type == CODIFY_CODE_TYPE_JS ) {
                    $file_buffer .= \JShrink\Minifier::minify( $file_contents, $mini_options );
                } else {
                    $file_buffer .= $this->compress_css( $file_contents );
                }
            }
            if ( $code_type == CODIFY_CODE_TYPE_JS ) {
                $file_buffer .= "\n;";
            }
        }

        // If we are not minifying...
        if ( !$this->minify ) {

            // Add a comment with the list of included files at the top of the generated file.
            $file_buffer = "/* -------------------- Generated " . date("Y-m-d H:i:s") . " ------------------------------ \n\n"  .
                           "\nFILES INCLUDED\n\n" .
                            $name_list .
                            "\n*/\n\n" .
                            $file_buffer;
        }

        // Figure out the name of the new file and open it for writing
        $file_name = $this->module_prefix . $module_name . "." .  date( "YmdHis" ) . "." . $code_type;
        $write_handle = fopen( $source_path . $file_name, "w" );
        if ( $write_handle !== false ) {

            // Write the contents of the file and close it.
            fwrite( $write_handle, $file_buffer );
            fclose( $write_handle );
        } else {

            // Write failed so return null
            $file_name = false;
        }

        return( $file_name );
    }


}


class Code_obj {

    public $info = "";
    public $timestamp = 0;

    function __construct( $info, $doc_root ) {
        $this->info = $info;
        $this->timestamp = filemtime( $doc_root .  $info );
    }
}



?>