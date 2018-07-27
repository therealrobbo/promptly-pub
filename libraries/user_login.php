<?php
/**
 * Libraries relating to site administrator (editor) privileges.
 *
 * This is a bit of overkill but it supports many of the now-unnecessary legacy functions from CBR's prior backend
 */

define( 'LOGIN_ERROR_NONE',           0 );
define( 'LOGIN_ERROR_NO_PRIV',        1 );
define( 'LOGIN_ERROR_BAD_COOKIE_VAL', 2 );
define( 'LOGIN_ERROR_NOT_LOGGED_IN',  3 );

class User_login {

    public  $gCI;
    public  $username    = null;
    public  $user_id     = null;
    private $hash        = null;

    public  $user_rec    = null;

    public $login_error = LOGIN_ERROR_NONE;

    private $error_msgs;

    function init( ) {
        $this->gCI->model( 'users' );

        $this->error_msgs = array(
            LOGIN_ERROR_NONE           => "Login Successful",
            LOGIN_ERROR_NO_PRIV        => "You do not have permission to access this feature.",
            LOGIN_ERROR_BAD_COOKIE_VAL => "You do not have security access to this feature.",
            LOGIN_ERROR_NOT_LOGGED_IN  => "You must be logged in to access this feature."
        );
    }

    function is_logged_in( $privilege = USER_PRIV_ENDUSER ) {

        $is_logged_in = false;

        // Is there a login cookie already?
        if ( isset($_COOKIE[$this->gCI->gPZ['admin_cookie']] ) ) {

            // Burst the cookie into its parameter parts
            list( $this->username, $this->user_id, $this->hash ) = explode(',',$_COOKIE[$this->gCI->gPZ['admin_cookie']]);

            // Verify there is a user with this ID
            $this->user_rec = $this->gCI->users->get( $this->user_id );
            if ( !empty( $this->user_rec ) && ( $this->username == $this->user_rec['name'] ) ) {

                // Now check the encrypted key against this username / passwprd / secret word
                if ( $this->login_hash( $this->user_rec ) == $this->hash ) {

                    // The cookie check's out with the user's info on record.
                    // Now check to make sure they have proper security clearance

                    // If the user has clearance
                    if ( $this->user_has_privilege( $this->user_rec, $privilege ) ) {

                        $is_logged_in = true;

                    } else {
                        // The user did not have valid security clearance so boot them out
                        $this->login_error = LOGIN_ERROR_NO_PRIV;
                    }
                } else {
                    // The user's cookie does not reflect the users name/id/password on record, so boot them out to login...
                    $this->login_error = LOGIN_ERROR_BAD_COOKIE_VAL;
                }
            } else {
                // The user's cookie does not reflect the users name/id/password on record, so boot them out to login...
                $this->login_error = LOGIN_ERROR_BAD_COOKIE_VAL;
            }
        } else {
            // There is no login cookie, so send them to login
            $this->login_error = LOGIN_ERROR_NOT_LOGGED_IN;
        }

        return( $is_logged_in );
    }


    public function set_login_cookie( $user_info, $delete = false, $permanent = false ) {
        setcookie(
            $this->gCI->gPZ['admin_cookie'],
            ( !$delete ? ( $user_info['name'] . ',' . $user_info['id'] . ',' . $this->login_hash( $user_info ) ) : '' ),
            ( !$delete ? ( $permanent ? time()+31536000 : 0 ) : time()-86400 ),
            "/"
        );
    }



    private function login_hash( $user_info ) {
        $salt   = "anchoviepizza";
        $pepper = "cheesygordita";

        return( md5( $user_info['name'] . $salt . $user_info['password'] . $pepper ) );
    }

    function user_has_privilege( $user_rec, $privilege ) {

        // Are we dealing with a list of privileges?
        if ( is_array( $privilege ) ) {

            // Assume no privilege
            $has_priv = false;

            // Loop through the list
            foreach ( $privilege  as $this_privilege ) {

                // Use this function in its individual privilege mode to check privilege
                $has_priv = $this->user_has_privilege( $user_rec, $this_privilege );

                // If they have any of the privileges we can stop looking
                if( $has_priv ) {
                    break;
                }
            }
        } else {

            // We are checking an individual privilege

            // First check if the group has this privilege
            $priv_mask = $user_rec['privilege'] & $privilege;
            $has_priv = !empty( $priv_mask );
        }

        // Return the result
        return( $has_priv );
    }


    public function get_error_message( $error_code ) {
        return( !isset( $this->error_msgs[$error_code] ) ? "Unknown login error" : $this->error_msgs[$error_code] );
    }

}