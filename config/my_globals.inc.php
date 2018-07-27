<?php
global $gEnvironment, $gPZ;


$gPZ['environment']         = $gEnvironment;
$gPZ['app_name']            = 'word.coffee';
$gPZ['app_slogan']          = 'wake up and write';
$gPZ['app_dir']             = '';
$gPZ['inquiries_email']     = 'admin@prompt.ly';
$gPZ['session_cookie']      = 'coffee_9dk30fl032lf';


switch ( $gPZ['environment'] ) {
    case ENV_LOC:
        $gPZ['doc_root']          = 'C:/Users/Rob/Documents/GitHub/promptly/';

        $gPZ['base_url']          = 'http://loc.prompt.ly';
        $gPZ['base_url_ssl']      = 'http://loc.prompt.ly';
        $gPZ['suppress_ads']      = false;
        $gPZ['ssl_environment']   = false;
        $gPZ['admin_email']       = 'robw@prompt.ly';
        $gPZ['admin_cookie']      = 'XmRZ20FK16L';
        $gPZ['db_config']         = array(
            "username"  => "root",
            "password"  => "",
            "database"  => "promptly",
            "hostname"  => "127.0.0.1"
        );
        $gPZ['session_cookie']    = 'coffee_9dk30fl032lfrob';
        $gPZ['uploads_path']      = $gPZ['doc_root'] . "../promptly-content/";
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_STRICT );
        break;

    case ENV_PROD:
        $gPZ['doc_root']          = '';     // TODO: Configure document root

        $gPZ['base_url']          = '';     // TODO: Configure base URL
        $gPZ['base_url_ssl']      = '';     // TODO: Configure base SSL
        $gPZ['suppress_ads']      = false;
        $gPZ['ssl_environment']   = false;
        $gPZ['admin_email']       = '';     // TODO: Configure admin email
        $gPZ['admin_cookie']      = '';     // TODO: Configure amin cookie name
        $gPZ['db_config']         = array(
            "username"  => "",              // TODO: Configure database username
            "password"  => "",              // TODO: Configure database password
            "database"  => "",              // TODO: Configure database name
            "hostname"  => ""               // TODO: Configure database host
        );
        $gPZ['uploads_path']        = "";   // TODO: Configure upload path
        $gPZ['session_cookie']      = 'coffee_9dk30fl032lf531';
// TODO        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_STRICT );
// TODO       ini_set( 'display_errors', 1 );
        error_reporting( 0 );
        break;

}


$gPZ['admin_url']   = $gPZ['base_url'] . "/retailers";
$gPZ['backend_url'] = $gPZ['base_url'] . "/pmp-admin";

$gPZ['genfile_path']        = $gPZ['uploads_path'] . "assets/gen/";

$gPZ['adzone_cache_path']   = $gPZ['genfile_path'];
$gPZ['adzone_cache_prefix'] = "august_";
$gPZ['adzone_cache_code']   = "f48857f6e3b3171db9a1f40c829a3eab";


define( 'BLANK_DATE', '0000-00-00 00:00:00' );

define( 'PW_SALT',   "scratch9" );
define( 'PW_PEPPER', "fluffy_defender_of_earth" );
define( 'PW_WHEAT',  "alice_malice" );

?>