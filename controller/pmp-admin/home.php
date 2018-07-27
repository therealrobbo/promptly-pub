<?php
/**
 *
 * Main controller for all Administrative backend pages
 *
 */


class Home extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

        $this->library( "logs" );
    }


    //----------------------- L A N D I N G / L O G I N ----------------------------------------------------------------

    public function index( $redir = '', $error_code = LOGIN_ERROR_NONE ) {
        $this->set_mode( MODE_ADMIN );
        if ( empty( $redir ) ) {
            $redir = $this->gPZ['backend_url'] . "/prompt";
        } else {
            $redir = $this->my_url_decode( $redir );
        }
        if ( $this->user_login->is_logged_in( USER_PRIV_ADMIN ) ) {
            header( 'location: ' . $redir );
            die();
        } else {

            $username  = strtolower( $this->get_var( 'username',  '' ) );
            $password  = $this->get_var( 'password',  '' );
            $redir     = $this->get_var( 'redir',     $redir );

            if ( $error_code != LOGIN_ERROR_NONE ) {
                $this->set_message( $this->user_login->get_error_message( $error_code ),  'danger' );
            }
            if ( !empty( $username ) ) {

                // Start logging
                $this->logs->set_logname( LOG_LOGINS );
                $this->logs->start( "--------- START LOGIN ATTEMPT $username -------------" );
                $this->logs->message( "username=" . $username );

                // Hash the pasworrd
                $this->logs->message( "password=" . $password );

                // Additional logging
                $this->logs->message( "redir=" . $redir);

                // Look up the user
                $user_rec = $this->users->get_by_login( $username, $password );
                if ( empty( $user_rec ) ) {
                    $this->logs->message( "Login error!!" );
                    $this->logs->end( "BAD USER" );
                    $this->set_message( 'The login information that you entered is invalid. Please try again', 'danger' );
                } else {

                    $this->logs->message( "Result ID = " . $user_rec['id'] );
                    $this->logs->message( "Result user = " . $user_rec['user'] );
                    $this->logs->message( "Result force PWD = " . $user_rec['force_pwd_change'] );
                    $this->logs->message( "Result active = " . $user_rec['active'] );
                    if ( $user_rec['active'] == '0' ) {
                        $this->logs->end( "INACTIVE USER" );
                        $this->set_message( "We're sorry but you are not permitted to access this page!!.", 'danger' );
                    } else {

                        //set logged in cookie
                        $this->user_login->set_login_cookie( array( 'name' => $user_rec['name'], 'id' => $user_rec['id'], 'password' => $user_rec['password'] ), false );

                        $this->logs->message( "Logged In!!!" );

                        //redirect user to account manager page
                        $this->logs->end( "redirecting to " . $redir );
                        header("Location: $redir");
                        exit();
                    }
                }
            }

            $this->data( 'template', 'login.php' );

            $this->data( 'username',  $username );
            $this->data( 'redir',     $redir );

            return( $this->view() );
        }
    }
}