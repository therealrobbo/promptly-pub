<?php
/**
 * Controller for cron tasks
 */

define( 'SUNDAY',    0 );
define( 'MONDAY',    1 );
define( 'TUESDAY',   2 );
define( 'WEDNESDAY', 3 );
define( 'THURSDAY',  4 );
define( 'FRIDAY',    5 );
define( 'SATURDAY',  6 );

define( 'SECONDS_IN_A_DAY', ( 24 * 60 * 60 ) );


class Cron extends My_Controller {

    private $home_link     = '';

    function __construct( $request ) {
        parent::__construct( $request );

        $this->library( 'logs' );
    }

    function init( ) {
        parent::init( );
    }


    function index( $args = '' ) {

        $now_day_of_week = date( "D" );
        $now_hours = date( "H" );
        $now_minutes = date( "i" );

        //--------------------- D A I L Y   T A S K S ------------------------------------------------------------------
        // Tasks to run once a day (at 1:00)
        if ( ( $now_hours == "01" ) && ( $now_minutes == "00" ) ) {
            $this->daily();
        }

        //--------------------- H O U R L Y   T A S K S ----------------------------------------------------------------
        // Tasks to run once an hour (at the top)
        if ( $now_minutes  == "00" ) {
            $this->hourly( );
        }


        //--------------------- E V E R Y   M I N U T E L Y   T A S K S ------------------------------------------------
        $this->every_minute( );
    }

    private function every_minute( ) {

    }

    private function hourly( ) {

    }

    private function daily( ) {

    }

}
