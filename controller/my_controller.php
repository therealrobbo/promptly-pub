<?php
/**
 * Override base controller for site-wide services
 */

define( 'MODE_FRONT',    0 );
define( 'MODE_ENDUSER',  1 );
define( 'MODE_ADMIN',    2 );
define( 'MODE_BARE',     3 );

define( 'URL_COLON_REPLACE', '_c_' );
define( 'URL_SLASH_REPLACE', '_s_' );


class My_Controller extends PZ_Controller {
    private $messages = null;

    private $default_template = '';

    private $page_title;
    private $mode;


    /**
     * Constructor method.
     *
     * @param $request - standard PZ requested method
     *
     * @param array $no_login - controller subclass can provide an array of methods that do not require the user to be logged in
     * @param array $method_auth - controller subclass can provide a table of authorization levels required
     *                             for methods within the controller
     *                             e.g. array( "index" => ADMIN_SEC_FREELANCER, "upgrade" => ADMIN_SEC_ADMIN ).
     *                             note: use special key "all" to assign all methods to a level
     */
    function __construct( $request ) {

        global $gPZ;

        parent::__construct( $request );

        // Refresh the gPZ local with the updated user info
        $this->gPZ = $gPZ;

        // Models

        // Libraries
        $this->library( 'database', 'db' );
        $this->library( 'codify' );
        $this->library( 'og_meta' );
        $this->codify->set_minify( ( $this->gPZ['environment'] == ENV_PROD  ) );
        $this->codify->set_prefix( "promptly_" );

        $this->data( 'app_name',  $this->gPZ['app_name'] );
        $this->data( 'site_name', $this->gPZ['app_name'] );

        $this->data( 'base_url',    $this->gPZ['base_url'] );
        $this->data( 'admin_url',   $this->gPZ['backend_url'] );

    }

    public function set_message( $message_text = '', $message_type = '' ) {
        if ( empty( $this->messages ) ) {
            $this->messages = array( );
        }
        $this->messages[] = array(
            'text'  => $message_text,
            'type'  => $message_type
        );

        $this->data( 'page_messages', $this->messages );
    }


    function set_page_title( $page_title ) {
        $this->page_title = $page_title;
    }

    function set_mode( $mode = MODE_FRONT ) {
        $this->mode = $mode;

        $this->asset_request( REQ_ASSET_CSS, "bootstrap" );
        $this->asset_request( REQ_ASSET_CSS, "bootstrap-theme" );

        $this->asset_request( REQ_ASSET_JS, "https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" );
        $this->asset_request( REQ_ASSET_JS, "bootstrap" );

        switch( $this->mode ) {
            case MODE_FRONT:
                $this->library( "user_login" );

                $this->model( "partner_codes" );
                $this->model( "sessions" );
                $this->model( "samples" );

                $partner_codes = $this->partner_codes->get_all_codes( );
                if ( !empty( $partner_codes ) ) {
                    $this->partner_codes->add_to_template( $partner_codes );
                }

                $this->set_page_title( $this->gPZ['app_name'] );
                $this->data( 'site_title',  $this->gPZ['app_name'] );
                $this->data( 'header_title',  $this->gPZ['app_name'] );
                $this->data( 'header_slogan', $this->gPZ['app_slogan'] );

                $this->asset_request( REQ_ASSET_CSS, "global_front" );

                $this->default_template = 'default';
                break;

            case MODE_BARE:
                $this->default_template = 'bare_data';
                break;

            case MODE_ENDUSER:
                $this->force_ssl();
                $this->force_no_cache();
                $this->codify->set_ssl( $this->gPZ['ssl_environment'] );

                $this->set_page_title( $this->gPZ['app_name'] . " Retailer Admin" );
                $this->default_template = 'enduser';

/*                $this->library( "facs_users" );

                if ( $this->facs_users->is_logged_in( ) ) {
                    $this->data( 'retailer_is_logged_in', true );
                    $this->data( 'my_stores', $this->facs->get_stores_for_user( $this->facs_users->loginid ) );
                    $this->data( 'user_display_name',
                        ( !empty( $this->facs_users->user_rec['user_fname'] ) ?
                            $this->facs_users->user_rec['user_fname'] . " " . $this->facs_users->user_rec['user_lname'] :
                            $this->facs_users->user_rec['user_name'] ) );
                } else {
                    $this->data( 'retailer_is_logged_in', false );
                    $this->data( 'my_stores',             array() );
                    $this->data( 'user_display_name',     '' );
                }

*/
                $this->data( 'site_name',    $this->gPZ['app_name'] . " Account" );


                $this->asset_request( REQ_ASSET_CSS, "global_retailers" );

                $this->asset_request( REQ_ASSET_JS, "https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" );
                $this->asset_request( REQ_ASSET_JS, "../bootstrap/js/bootstrap.min", false );
                $this->asset_request( REQ_ASSET_JS, "../bootstrap/js/bootstrap-datepicker", false );

                break;

            // --------------------------- ADMIN MODE ------------------------------------------------------------------
            case MODE_ADMIN:
                $this->force_ssl();
                $this->force_no_cache();
                $this->codify->set_ssl( $this->gPZ['ssl_environment'] );

                $this->set_page_title( $this->gPZ['app_name'] . " Admin" );
                $this->default_template = 'admin/default';

                $this->library( "user_login" );

                if ( $this->user_login->is_logged_in( USER_PRIV_ADMIN ) ) {
                    $this->data( 'admin_is_logged_in', true );
                    $this->data( 'user_display_name', $this->user_login->user_rec['name'] );
                } else {
                    $this->data( 'admin_is_logged_in', false );
                    $this->data( 'user_display_name',  '' );
                }

                $this->data( 'site_name',    $this->gPZ['app_name'] . " Admin" );

                $this->asset_request( REQ_ASSET_CSS, "global_admins" );

                $this->asset_request( REQ_ASSET_JS, "batcave_forms" );

                break;
        }
    }

    /**
     * Request an asset (CSS or JS) be loaded into the template
     *
     * @param $type - REQ_ASSET_*
     * @param $name - the name of the file without the extension
     * @param $use_codify - use the codify bundler/minifier on this file
     */
    public function asset_request( $type, $name, $use_codify = true ) {

        // Is it a foreign resources (like the jquery base or a google lib)?
        if ( substr_compare( $name, "http", 0, 4 ) == 0 ) {
            $use_codify = false;
        }

        if ( $use_codify ) {
            if ( $type == REQ_ASSET_CSS ) {
                $this->codify->add_css( $name );
            } else {
                $this->codify->add_js( $name );
            }
        } else {
            parent::asset_request( $type, $name );
        }
    }

    /**
     * Template function that returns the code to include the requested assets in the template
     *
     * @param $type
     */
    public  function asset_retrieve( $type ) {

        if ( $type == REQ_ASSET_CSS ) {
            if ( $this->codify->css_count( ) > 0 ) {
                parent::asset_request( REQ_ASSET_CSS,  $this->codify->get_css( ) );
            }
        } else {
            if ( $this->codify->js_count( ) > 0 ) {
                parent::asset_request( REQ_ASSET_JS,  $this->codify->get_js( ) );
            }
        }

        parent::asset_retrieve( $type );
    }






    function get_var( $var_name, $default_value = '' ) {

        return( isset( $this->gPZ['post_vars'][$var_name] ) ? $this->gPZ['post_vars'][$var_name] : $default_value );
    }


    private function force_ssl( ) {
        if ( $this->gPZ['ssl_environment'] ) {
            if( $_SERVER["HTTPS"] != "on" ) {
                header( "Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
                exit();
            }
        }
    }

    private function force_no_cache( ) {
        if ( $this->gPZ['cache_environment'] ) {

            if( ( $_SERVER['SERVER_PORT'] != '8080' ) ) {
                header( "Location: " . $this->gPZ['base_url_no_cache'] . $_SERVER["REQUEST_URI"] );
                exit();
            }
        }
    }


    function init( ) {

        // Call the base init function to fire all libraries and models
        parent::init();
    }



    public function gen_page_title( $subtitle = '' ) {
        $page_title = $this->page_title;
        if ( !empty( $subtitle ) ) {
            $page_title = $subtitle . " | " .  $page_title;
        }

        return( $page_title );
    }

    public function view( $template_name = '', $display = true ) {

        $template_name = ( !empty( $template_name ) ? $template_name : $this->default_template );

        $this->data( 'og_meta', $this->og_meta->get_for_template() );

        return( parent::view( $template_name, $display ) );
    }

    /**
     * Add Open Graph meta data for this page
     *
     * @param $property
     * @param $value
     * @param string $extra_property
     */
    public  function add_og_meta( $property, $value, $extra_property = 'og' ) {
        $this->og_meta->add( $property, $value, $extra_property );
    }



    // ---------------------------- A D M I N   S H A R E D   F U N C T I O N S ----------------------------------------

    /**
     * For admin controllers, check if users is logged in. If not redirect to the login page. If they are, return (TRUE)
     *
     * @param $destination_url - the URL to direct back to after login
     * @param int $error_code - any error codes to be relayed to the login page
     *
     * @return bool
     */
    public function admin_auth( $destination_url,  $error_code = LOGIN_ERROR_NOT_LOGGED_IN ) {

        $this->set_mode( MODE_ADMIN );
        if ( !$this->user_login->is_logged_in() ) {
            $location_url = $this->gPZ['backend_url'] . "/home";
            $location_url .= "/" . urlencode( $this->my_url_encode( $destination_url ) );
            if ( $error_code != LOGIN_ERROR_NONE )
                $location_url .= "/" . $error_code;

            header( "Location: " . $location_url );
            die();
        }

        return( true );
    }

    public function my_url_encode( $string ) {
        return(
            str_replace( "/", URL_SLASH_REPLACE,
                str_replace( ":", URL_COLON_REPLACE, $string )
            )
        );
    }
    public function my_url_decode( $string ) {
        return(
        str_replace( URL_SLASH_REPLACE, "/",
            str_replace( URL_COLON_REPLACE, ":", $string )
        )
        );
    }


    public function admin_render_pager( $search_args, $list_page, $filters, $max_page, $pager_span = '4', $pager_size = 'mini', $text_filter = 'txt_keyword' ) {

        $this->data( 'pager_vars', array(
            'search_args' => $search_args,
            'list_page'   => $list_page,
            'text_filter' => $text_filter,
            'filters'     => $filters,
            'max_page'    => $max_page,
            'pager_span'  => $pager_span,
            'pager_size'  => $pager_size
        ) );

        return( $this->view( 'admin/pager.php', false ) );
    }

    function admin_render_column_heads( $columns, $search_args, $post_url ) {
        $colhead_renders = array();
        foreach( $columns as $column_title => $sort_field ) {
            $this->data(
                'colhead_args', array(
                'url'          => $post_url,
                'reverse_dir'  => ( ( $search_args['order_dir'] == 'DESC' ) ? 'ASC' : 'DESC' ),
                'icon_dir'     => ( ( $search_args['order_dir'] == 'DESC' ) ? '&#x25BC;' : '&#x25B2;' ),
                'search_args'  => $search_args,
                'column_title' => $column_title,
                'sort_field'   => $sort_field
            ) );

            $colhead_renders[$column_title] = $this->view( 'admin/column_head.php', false );
        }

        return( $colhead_renders );
    }


}

?>
