<?php
/**
 *
 * Main controller logging out of admin
 *
 */


class Logout extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );
    }

    // ------------------------ L O G O U T ----------------------------------------------------------------------------

    public function index( ) {
        $this->set_mode( MODE_ADMIN );
        if ( $this->user_login->is_logged_in() ) {
            $user_rec = $this->user_login->user_rec;
        } else {
            $user_rec = $this->users->dummy();
        }

        $this->user_login->set_login_cookie( $user_rec, true );

        header( "Location: " . $this->gPZ['backend_url'] );
        exit();
    }
}