<?php
/**
 * Story flow management
 */
require_once( "config/config.inc.php" );
require_once("core/globals.php");
global $gPZ, $gEnvironment;

// Load app specific globals
if ( file_exists( "config/my_globals.inc.php" ) ) {
    require_once( "config/my_globals.inc.php" );
}


// For command-line app runs, convert command line args into $_REQUEST super
global $argc;
if ( ( count( $_REQUEST ) == 0 ) && ( $argc > 0 ) ) {
    parse_str(implode('&', array_slice($argv, 1)), $_REQUEST);
}

core_burst_args( $_REQUEST );

// If the specified controller doesn't exist, divert to the default controller
if ( !file_exists( $gPZ['controller_path'] . $gPZ['controller'] . '.php' ) ) {
    $gPZ['controller']      = $gPZ['default_controller'];
}

// Load the controller
require_once( $gPZ['controller_path'] . $gPZ['controller'] . '.php' );
$controller_name = ucfirst( $gPZ['controller'] );
$gPZ['controller_instance'] = new $controller_name( $gPZ['request'] );

// We're logged in and good to go
$gPZ['controller_instance']->init( );
$gPZ['controller_instance']->run( );
?>