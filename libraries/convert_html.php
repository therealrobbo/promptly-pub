<?php
/**
 * HTML COnverters
 */


require_once( 'converters/class_rtf.php');

define( 'CONVERT_TYPE_HTML',     'html' );
define( 'CONVERT_TYPE_RTF',      'rtf' );
define( 'CONVERT_TYPE_TXT',      'txt' );
define( 'CONVERT_TYPE_FOUNTAIN', 'fountain' );


/**
 * Wrapper for the database.
 */

class Convert_html
{

    public $gCI;

    private $type = null;
    private $valid_types = array( CONVERT_TYPE_HTML, CONVERT_TYPE_TXT, CONVERT_TYPE_RTF, CONVERT_TYPE_FOUNTAIN );

    private $mime_types = array(
        CONVERT_TYPE_HTML     => 'text/html',
        CONVERT_TYPE_TXT      => 'text/plain',
        CONVERT_TYPE_RTF      => 'application/rtf',
        CONVERT_TYPE_FOUNTAIN => 'text/plain'
    );
    private $file_prefix = "word_coffee_writing_";



    /**
     * Constructor
     *
     */
    function __construct() {
    }

    function init() {

    }

    /**
     * Constructor
     * @param $type - One of the types defined above
     */
    public function set_type( $type = false ) {
        if ( in_array( $type, $this->valid_types ) ) {
            $this->type = $type;
        }
    }

    private function convert_to_text( $html ) {
        return( strip_tags( str_replace( "</p>", "</p>\r\n\r\n", $html ) ) );
    }

    private function convert_to_rtf( $html ) {
        require_once( 'converters/class_rtf.php' );

        $rtf = new rtf( 'rtf_config.php' );
        $rtf->setPaperSize(5);
        $rtf->setPaperOrientation(1);
        $rtf->setDefaultFontFace(0);
        $rtf->setDefaultFontSize(24);
        $rtf->setAuthor( $this->gCI->gPZ['app_name'] );
        $rtf->setTitle( $this->gCI->gPZ['app_name'] . " Writing Sample");
        $rtf->addColour("#000000");
        $rtf->addText( $html );
        return( $rtf->get_text() );
    }


    public function convert( $html, $type = null ) {
        $return_text = $html;
        if ( !empty( $type ) ) {
            $this->set_type( $type );
        }
        if ( !empty( $this->type ) ) {

            switch( $this->type ) {
                case CONVERT_TYPE_RTF:
                    $return_text = $this->convert_to_rtf( $html );
                    break;

                case CONVERT_TYPE_TXT:
                    $return_text = $this->convert_to_text( $html );
                    break;

                case CONVERT_TYPE_FOUNTAIN:
                    break;
            }
        }

        return( $return_text );
    }

    public function get_mime(  ) {
        return( isset( $this->mime_types[$this->type] ) ? $this->mime_types[$this->type] : 'application/octet-stream' );
    }

    public  function get_filename( ) {
        return( $this->file_prefix . date( "Y-m-d" ) . "." . $this->type );
    }

}


?>