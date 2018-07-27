<?php
/**
 * Functions supporting Opengraph meta data to be inserted into the headers of our pages
 */


define( 'OG_DATA_TYPE_STRING',   1 );
define( 'OG_DATA_TYPE_DATETIME', 2 );
define( 'OG_DATA_TYPE_INTEGER',  3 );
define( 'OG_DATA_TYPE_FLOAT',    4 );

define( 'OG_APP_ID', '133017523404979' );

/**
 * Planet Krypton Progress Bar
 */

class Og_meta {

    public $gCI;

    public $dictionary = array(
        'url'           => array( 'vartype' => OG_DATA_TYPE_STRING ),
        'title'         => array( 'vartype' => OG_DATA_TYPE_STRING ),
        'image'         => array(
            'vartype' => OG_DATA_TYPE_STRING,
            'extra_properties' => array(
                'url'        => array( 'vartype' => OG_DATA_TYPE_STRING ),
                'secure_url' => array( 'vartype' => OG_DATA_TYPE_STRING ),
                'type'       => array( 'vartype' => OG_DATA_TYPE_STRING ),
                'width'      => array( 'vartype' => OG_DATA_TYPE_INTEGER ),
                'height'     => array( 'vartype' => OG_DATA_TYPE_INTEGER ),
            )
        ),
        'description'   => array( 'vartype' => OG_DATA_TYPE_STRING ),
        'site_name'     => array( 'vartype' => OG_DATA_TYPE_STRING ),
        'type'          => array( 'vartype' => OG_DATA_TYPE_STRING ),
        'article'       => array(
            'vartype'   => OG_DATA_TYPE_STRING,
            'extra_properties' => array(
                'published_time'    => array( 'vartype' => OG_DATA_TYPE_DATETIME ),
                'modified_time'     => array( 'vartype' => OG_DATA_TYPE_DATETIME ),
                'expiration_time'   => array( 'vartype' => OG_DATA_TYPE_DATETIME ),
                'section'           => array( 'vartype' => OG_DATA_TYPE_STRING ),
                'tag'               => array( 'vartype' => OG_DATA_TYPE_STRING ),
            )
        ),
        'fb' => array(
            'vartype' => OG_DATA_TYPE_STRING,
            'extra_properties' => array(
                'app_id'    => array( 'vartype' => OG_DATA_TYPE_STRING )
            )
        )
    );

    private $vals = array();

    function __construct(  ) {

    }

    public function init( ) {
        $this->gCI->library( 'util' );
        $this->add( 'type',      'article' );
        $this->add( 'site_name', $this->gCI->gPZ['app_name'] );
        $this->add( 'app_id',    OG_APP_ID, 'fb' );
    }


    public function add( $property, $value, $context = 'og' ) {

        $master = ( ( $context == 'og' ) ? $property : $context );
        if ( isset( $this->dictionary[$master] ) ) {
            $property_val  = $value;
            if ( ( $context != 'og' ) ) {
                if ( isset( $this->dictionary[$master]['extra_properties'] ) &&  isset( $this->dictionary[$master]['extra_properties'][$property] ) ) {
                    $property_type  = $this->dictionary[$master]['extra_properties'][$property]['vartype'];
                } else {
                    throw new Exception( $property . 'is not a recognized extra property of ' . $context );
                }
            } else {
                $property_type = $this->dictionary[$property]['vartype'];
            }

            switch( $property_type ) {
                case OG_DATA_TYPE_STRING:
                    $property_val = htmlentities( strval( strip_tags( html_entity_decode( $this->gCI->util->display_text( $property_val ) ) ) ) );
                    break;

                case OG_DATA_TYPE_DATETIME:
                    $property_val = date( "c", strtotime( $property_val ) );
                    break;

                case OG_DATA_TYPE_INTEGER:
                    $property_val = intval( $property_val );
                    break;

                case OG_DATA_TYPE_FLOAT:
                    $property_val = floatval( $property_val );
                    break;
            }

            $this->vals[] = array(
                'property'  => $context . ':' . $property,
                'content'   => $property_val
            );
        } else {
            throw new Exception( 'Unknown OG Metadata type - ' . $property );
        }
    }

    function get_for_template( ) {
        return( $this->vals );
    }
}
