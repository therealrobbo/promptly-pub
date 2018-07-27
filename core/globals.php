<?php
global $gEnvironment;

// Phantom Zone Globals
$gPZ = array(); // Phantom Zone globals
$gPZ['app_name']                = 'Configure App Name in /config/my_globals.inc.php';
$gPZ['app_dir']                 = '.';
$gPZ['view_dir']                = 'view';
$gPZ['model_dir']               = 'model';
$gPZ['lib_dir']                 = 'libraries';
$gPZ['css_dir']                 = 'assets/css';
$gPZ['js_dir']                  = 'assets/js';
$gPZ['user_name']               = '';
$gPZ['user_id']                 = 0;
$gPZ['user_author_rec']         = null;
$gPZ['user_admin_rec']          = null;
$gPZ['sec_group']               = '';
$gPZ['lib_root']                = "./";
$gPZ['default_controller']      = 'home';
$gPZ['default_controller_path'] = 'controller/';
$gPZ['controller']              = "";
$gPZ['controller_path']         = 'controller/';
$gPZ['controller_instance']     = null;
$gPZ['request']                 = array( );
$gPZ['post_vars']               = array();
$gPZ['environment']             = $gEnvironment;

define( 'AJAX_RESPONSE_SEPARATOR', "::" );


/**
 * Burst request vars from URL or POST into global variables.
 *
 * @param $args
 */
function core_burst_args( $args ) {

    if ( !empty( $args ) ) {
        global $gPZ;

        $arg_array              = explode( "/", $args['a'] );
        $gPZ['controller']      = strtolower( array_shift( $arg_array ) );
        while ( !empty( $gPZ['controller'] ) && is_dir(  $gPZ['controller_path'] . $gPZ['controller']  ) ) {
            $gPZ['controller_path'] .= $gPZ['controller'] . "/";
            $gPZ['controller']      = strtolower( array_shift( $arg_array ) );
        }

        $gPZ['request']    = implode("/", $arg_array );

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            foreach( $_POST as $key => $value ) {
                $gPZ['post_vars'][$key] = $value;
            }
        }
    }
}

/**
 * Oft-used debugging function to make print_r print out pretty in the browser window
 *
 * @param $object
 */
function pretty_r( $object ) {
    $search_chars  = array( " ",     "<",    ">" );
    $replace_chars = array( "&nbsp", "&lt;", "&gt;" );
    print( nl2br( str_replace( $search_chars, $replace_chars, print_r( $object, true ) ) ) );
}


require_once('controller.php');
require_once('model.php');
if ( file_exists( "controller/my_controller.php" ) ) {
    require_once('controller/my_controller.php');
}
?>