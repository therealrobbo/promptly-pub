<?php
global $gEnvironment;

define( 'ENV_PROD', 1 );

define( 'ENV_DEV',  2 );
define( 'ENV_DEV2', 3 );
define( 'ENV_DEV3', 4 );
define( 'ENV_DEV4', 5 );

define( 'ENV_LOC',  10 );

include( 'environment.inc.php' );


if ( $gEnvironment != ENV_PROD ) {
    error_reporting( E_ALL ^ E_DEPRECATED );
    ini_set( 'display_errors', 1 );
}

?>