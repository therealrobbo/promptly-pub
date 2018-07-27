<?php

define( "REQ_ASSET_CSS",        'css' );
define( "REQ_ASSET_JS",         'js' );
define( "REQ_ASSET_JS_GLOBAL",  'jsg' );

/**
 * The core controller class, with basic controller functions
 *
 * Class PZ_Controller
 */
class PZ_Controller {
    public $gPZ;

    public $method             = "index";
    public $method_args        = array();
    public $view_data          = array();
    public $default_view       = "default";
    public $models_loaded      = array();
    public $libs_loaded        = array();

    private $load_assets   = null;
    private $lib_init_started = false;

    /**
     * Constructor
     *
     * @param $request
     */
    public function __construct( $request ) {

        global $gPZ;

        $this->gPZ = $gPZ;

        $this->set_method( $request );
    }


    function init_lib( $library_name ) {

        if ( property_exists ( $this->$library_name, 'gCI' ) ) {
            $this->$library_name->gCI = $this->gPZ['controller_instance'];
        }
        if ( method_exists( $this->$library_name, 'init' ) ) {
            $this->$library_name->init();
        }
    }

    function init( ) {

        global $gPZ;

        // Set up the gPZ global as an property of this object
        $this->gPZ = $gPZ;

        foreach( $this->models_loaded as $model_name ) {
            $this->$model_name->gCI = $this->gPZ['controller_instance'];
            if ( method_exists( $this->$model_name, 'init' ) ) {
                $this->$model_name->init();
            }
        }
        $this->lib_init_started = true;
        foreach( $this->libs_loaded as $library_name ) {

            $this->init_lib( $library_name );
        }
    }

    /**
     * If something unrecoverable happens a controller can call this to die with a error message
     *
     * @param $message
     */
    function fatal_error( $message ) {
        $this->data( 'message', $message );
        $this->view( 'error_dump.php' );
        die();
    }


    /**
     * Load the specified VIEW into memory and, optionally, output it.
     *
     * @param string $template_name - the name of the view/template to load
     * @param bool $display - true to output the rendered view or false to suppress it
     *
     * @return string - the rendered view
     */
    public function view( $template_name = '', $display = true ) {

        global $gPZ;

        // Start output buffering
        ob_start();

        // Burst the view data
        foreach( $this->view_data as $key => $value ) {
            $$key = $value;
        }

        if ( empty( $template_name ) ) {
            $template_name = $this->default_view;
        }

        // Include the template
        if ( strstr( $template_name, ".php" ) == false ) {
            $template_name .= ".php";
        }
        include( $this->gPZ['view_dir'] . "/" . $template_name );

        // Render the template
        $view_render = ob_get_contents();
        ob_end_clean();

        if ( $display )
            print( $view_render );

        return( $view_render );
    }

    /**
     * Add a piece of data to the data set for this view
     *
     * @param $key - the variable name of the data
     * @param $value - the value of the data
     */
    public function data( $key, $value ) {
        $this->view_data[$key] = $value;
    }


    /**
     * Default function for all controllers. Each controller extension should define its own index function or else this
     * one will trigger and display an error message.
     *
     * @param $request
     */
    public function index( $request ) {
        die( "FATAL: No index defined for the " .  strtoupper( $this->gPZ['controller'] ) . " controller <br />" .
            "REQUEST STRING: " . $request );
    }


    /**
     * Run the specified method with the specified args (methods and args are specified from inbound GET/POST arguments)
     */
    public function run(  ) {
        call_user_func_array( array( $this, $this->method ), $this->method_args );
    }

    /**
     * Set the method based on the request string passed in from the URL
     *
     * @param $request
     */
    private function set_method( $request ) {

        // If there is a request string
        if ( !empty( $request ) || ( ( $_SERVER['REQUEST_METHOD'] == 'POST' ) && !empty( $this->gPZ['post_vars'] ) ) ) {

            // Break the request string into an array
            $request_array    = explode( "/", $request );

            // Use the first item in the request string as a method candidate
            $method_candidate = array_shift( $request_array );

            // If this object has a method that matches the candidate...
            if ( method_exists( $this, $method_candidate ) ) {

                // ...set the method from the candidate
                $this->method = $method_candidate;
            } else {

                // We're sticking with the default method, so put the method_candidate
                // back into the array.
                array_unshift( $request_array, $method_candidate );
            }

            // Fire up reflection method to try to figure out what the args are for this method
            $r = new ReflectionMethod( $this, $this->method );
            $params = $r->getParameters();

            // Are there parameters for this method?
            if ( !empty( $params ) && is_array( $params ) && ( count( $params ) > 0 ) ) {

                // Start saving the args
                $param_count = count( $params );

                // Look through the param array
                foreach ( $params as $param ) {

                    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
                        $param_name = $param->getName();
                        if ( isset( $this->gPZ['post_vars'][$param_name] ) ) {
                            $this->method_args[] = $this->gPZ['post_vars'][$param_name];
                        } else {
                            $this->method_args[] = $param->getDefaultValue();
                        }
                    } else {

                        // Decrement the parameter count
                        $param_count--;

                        // If there are still items from the request array...
                        if ( !empty( $request_array ) && is_array( $request_array ) && ( count( $request_array ) > 0 ) ) {

                            // And if we're not on the last argument for this method
                            if ( $param_count > 0 ) {

                                // Take the next argument off the array as our parameter value.
                                $param_value = array_shift( $request_array );
                            } else {
                                // Last argument, so rebuild any fragments that are left into a single string
                                $param_value = implode( "/", $request_array );
                            }
                        } else {
                            // No more parameters so set the value to the default
                            $param_value = $param->getDefaultValue();
                        }

                        // Now save the value in the parameter args
                        $this->method_args[] = $param_value;
                    }
                }
            }
        }
    }


    public function model( $model, $name = '' ) {

        global $gPZ;

        // First see if there is such a model in the model dir
        $model_file = $this->gPZ['model_dir'] . "/" . $model . ".php";
        if ( !file_exists( $model_file ) ) {
            $this->fatal_error( "Unknown MODEL requested '" . $model  . "'" .
                                ( !empty( $name ) ? ", name='" . $name . "'" : '' ) .
                                ", file='" . $model_file . "'" );
        }

        // If they left the name blank, then the name is the same as the model
        if ( empty( $name ) )
            $name = $model;

        // Was this model already loaded?
        if ( in_array( $name, $this->models_loaded ) ) {
            // We good!
            return;
        }

        // Is there already something with this name loaded?
        if ( isset( $this->$name ) ) {
            $this->fatal_error( "The model name you requested conflicts with a name that is already in use: '" . $name  . "' " );
        }

        // This is a new model. Lets load it up!
        require_once( $model_file );
        $model = ucfirst( $model );

        // Spawn a new instance and save it to the list.
        $this->$name = new $model();
        $this->models_loaded[] = $name;
    }


    public function library( $library, $name = '' ) {

        // First see if there is such a model in the model dir
        $library_file = $this->gPZ['lib_dir'] . "/" . $library . ".php";
        if ( !file_exists( $library_file ) ) {
            $this->fatal_error( "Unknown LIBRARY requested '" . $library  . "'" .
                                    ( !empty( $name ) ? ", name='" . $name . "'" : '' ) .
                                    ", file='" . $library_file . "'" );
        }

        // If they left the name blank, then the name is the same as the library
        if ( empty( $name ) )
            $name = $library;

        // Was this model already loaded?
        if ( in_array( $name, $this->libs_loaded ) ) {
            // We good!
            return;
        }

        // Is there already something with this name loaded?
        if ( isset( $this->$name ) ) {
            $this->fatal_error( "The model name you requested conflicts with a name that is already in use: '" . $name  . "' " );
        }

        // This is a new model. Lets load it up!
        require_once( $library_file );
        $library = ucfirst( $library );

        // Spawn a new instance and save it to the list.
        $this->$name = new $library();
        $this->libs_loaded[] = $name;

        // If we already ran the library init process
        if ( $this->lib_init_started ) {

            // It's safe to initialize this library.
            $this->init_lib( $name );
        }
    }


    /**
     * Request an asset be loaded into the template
     *
     * @param $type
     * @param $name
     */
    public function asset_request( $type, $name ) {

        if ( empty( $this->load_assets ) ) {
            $this->load_assets = array( );
        }

        if ( !isset( $this->load_assets[$type] ) ) {
            $this->load_assets[$type] = array();
        }

        if ( !in_array( $name, $this->load_assets[$type] ) ) {
            $this->load_assets[$type][] = $name;
        }
    }

    /**
     * Template function that returns the code to include the requested assets in the template
     *
     * @param $type
     */
    public  function asset_retrieve( $type ) {

        // Were any assets requested via the asset_request function?
        if ( !empty( $this->load_assets ) ) {

            // Were any assets of the speified type requested?
            if ( isset( $this->load_assets[$type] ) ) {

                $echo_string = "";

                if ( $type ==  REQ_ASSET_JS_GLOBAL ) {
                    $echo_string .= "<script type='text/javascript'>";
                }

                $version_string = ( ( $this->gPZ['environment'] != ENV_PROD ) ? "static" : date("Ymdh") );

                // Loop through the requested asset
                foreach( $this->load_assets[$type] as $asset_name ) {

                    $asset_path = ( !empty( $this->gPZ['app_dir'] ) ? "/" . $this->gPZ['app_dir'] : '' );

                    switch( $type ) {
                        case REQ_ASSET_CSS:
                            if ( substr_compare( $asset_name, "http", 0, 4 ) != 0 ) {
                                $asset_path .= "/" . $this->gPZ['css_dir'] . "/" . $asset_name;
                                if ( strstr( $asset_name, ".css" ) === false ) {
                                    $asset_path .= ".css";
                                }
                                $asset_path  .= "?v=" . $version_string;
                            } else {
                                $asset_path = $asset_name;
                            }
                            $echo_string .= '<link rel="stylesheet" href="' . $asset_path . '" type="text/css" />' . "\n";
                            break;

                        case REQ_ASSET_JS:
                            if ( substr_compare( $asset_name, "http", 0, 4 ) != 0 ) {
                                $asset_path .= "/" . $this->gPZ['js_dir'] . "/" . $asset_name;
                                if ( strstr( $asset_name, ".js" ) === false ) {
                                    $asset_path .= ".js";
                                }
                                $asset_path  .= "?v=" . $version_string;
                            } else {
                                $asset_path = $asset_name;
                            }

                            $echo_string .= '<script src="' .  $asset_path . '" type="text/javascript"></script>' . "\n";
                            break;

                        case REQ_ASSET_JS_GLOBAL:
                            $echo_string .= "var " . $asset_name . ";\n";
                            break;

                        default:
                            $echo_string .= '<!-- ERROR - Unknown Asset Type "' . $type . '" - "' . $asset_name . '" -->' . "\n";
                            break;
                    }
                }

                if ( $type ==  REQ_ASSET_JS_GLOBAL ) {
                    $echo_string .= "</script>";
                }

                echo $echo_string;
            }
        }
    }

}
?>