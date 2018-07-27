<?php
/**
 * Helper functions for managing and serving our images through the images controller
 */

define( 'IMGSRV_FOLDER_FACS',           'csl' );

class Image_helpers {

    public $gCI;
    public $image_base_path, $image_cache_path, $image_upload_path;

    private $base_url = '/images';

    // Various image handling functions
    private $handlers = array(
        'jpg'  => array('read' => 'imagecreatefromjpeg', 'write' => 'imagejpeg', 'cl' => 90, 'cmax' => 100 ),
        'jpeg' => array('read' => 'imagecreatefromjpeg', 'write' => 'imagejpeg', 'cl' => 90, 'cmax' => 100 ),
        'gif'  => array('read' => 'imagecreatefromgif',  'write' => 'imagegif',  'cl' => 0,  'cmax' => 0 ),
        'png'  => array('read' => 'imagecreatefrompng',  'write' => 'imagepng',  'cl' => 1,  'cmax' => 0 )
    );


    function init( ) {
        $this->image_base_path   = $this->gCI->gPZ['uploads_path'] . 'assets/images/csl/';
        $this->image_cache_path  = $this->image_base_path . 'cache/';
        $this->image_upload_path = $this->image_base_path . 'ul/';
    }

    /**
     * @param $upload_path
     * @param $image_name
     * @param $width
     * @param $height
     * @param bool $exact
     * @return bool
     */
    function validate_size( $upload_path, $image_name, $width, $height, $exact = true ) {

        $return_val = true;
        if ( !empty( $upload_path ) && !empty( $image_name ) ) {

            $image_size = $this->get_image_size( $upload_path, $image_name );
            if ( $exact )
                $return_val = ( ( $image_size[0] == $width ) && ( $image_size[1] == $height ) );
            else
                $return_val = ( ( $image_size[0] >= $width ) && ( $image_size[1] >= $height ) );
        }

        return( $return_val );
    }

    /**
     * Get the pixel dimensions of an image from the image server
     *
     * @param $upload_path
     * @param $image_name
     *
     * @return mixed - similar to getimagesize()
     */
    function get_image_size( $upload_path, $image_name ) {

        $file_path  = $upload_path . $image_name;

        return( getimagesize( $file_path ) );
    }

    /**
     * Get the pixel dimensions of an image from the image server
     *
     * @param $upload_path
     * @param $image_name
     *
     * @return mixed - similar to getimagesize()
     */
    function get_image_filesize( $upload_path, $image_name ) {

        $file_path  = $upload_path . $image_name;

        return( filesize( $file_path ) );
    }

    /**
     * Build a correctly formatted URL for the image server.
     *
     * @param $image_name
     * @param int $width
     * @param int $height
     * @param int $crop
     * @param bool $censor
     * @return string
     */
    function build_url( $image_name, $width=0, $height=0, $crop=1, $censor = false, $file_size = false, $force_cache = false  ) {

        $return_url = ( ( !$force_cache && ( $_SERVER['SERVER_PORT'] == '8080' ) ) ? $this->gCI->gPZ['base_url_no_cache'] : $this->gCI->gPZ['base_url'] );
        $return_url .= $this->base_url . "/$width/$height/$crop/$image_name";
        $return_url .= ( $censor ? "/1" : ( $file_size ? '/0' : '' ) );
        $return_url .= ( $file_size ? '/1' : '' );

        return( $return_url );
    }


    function scrub_filename( $filename ) {

        // Strip away any surrounding whitespace
        $return_name = trim( $filename );

        // Replace non-alpha numerics with hyphen
        $return_name = preg_replace( '/[^A-Za-z0-9]+/', '-', $return_name );

        // Replaces one or more occurrences of a hyphen, with a single one.
        $return_name = preg_replace('/[-]{2,}/', '-', $return_name);

        // This ensures that our string doesn't start or end with a hyphen.
        $return_name = trim($return_name, '-');

        return ( $return_name );

    }


    /**
     * Given a file name, make sure it does not conflict with an existing file in the target path, and return an altered the
     * file name if it does.
     *
     * @param $target_path
     * @param $file_name
     *
     * @return string
     */
    function unique_filename( $target_path, $file_name ) {

        // Try to preserve the origin name of the image, but add a suffix to ensure uniqueness
        $file_name_parts    = explode( '.', $file_name );
        $extension          = strtolower( array_pop( $file_name_parts ) );
        $file_name          = $this->scrub_filename( implode('.', $file_name_parts ) );
        $new_file_name      = $file_name . "." . $extension;
        $new_file_path      = $target_path . $new_file_name;
        $unique_id          = 1;
        while ( file_exists( $new_file_path ) ) {
            $new_file_name = $file_name . "_" . $unique_id . "." . $extension;
            $new_file_path = $target_path . $new_file_name;
            $unique_id++;
        }
        return( $new_file_name );
    }

    /**
     * Copy an uploaded file to it's appropriate destination. The name of the new file will be returned in the new_file_name
     * argument. If something went wrong an error message is returned.
     *
     * @param $field_name - the name of the field from the upload form.
     * @param $upload_path - the subdirectory where the image file should go (usually based on folder/usage )
     * @param $new_file_name - will be filled with the new file name, or left blank if something goes wrong.
     * @return string
     */
    function process_uploaded_image( $field_name, &$new_file_name ) {

        $valid_types   = array( "image/jpeg", "image/gif", "image/png" );
        $return_msg    = '';
        $new_file_name = '';

        // Did they attempt to upload the poll image?
        if ( isset( $_FILES[$field_name] ) ) {

            // Did the upload work?
            if ( $_FILES[$field_name]['error'] == UPLOAD_ERR_OK ) {

                // Did they upload one of the valid file types
                if ( in_array( $_FILES[$field_name]['type'], $valid_types ) ) {

                    $image_type =  exif_imagetype( $_FILES[$field_name]["tmp_name"] );
                    if ( $image_type !== false ) {
                        // Try to preserve the origin name of the image, but add a suffix to ensure uniqueness
                        $new_file_name = $this->unique_filename( $this->image_upload_path, $_FILES[$field_name]["name"] );
                        $new_file_path = $this->image_upload_path . $new_file_name;

                        move_uploaded_file( $_FILES[$field_name]["tmp_name"], $new_file_path );
                    } else {
                        $return_msg = "The image file is corrupted. Please check it and try again.";
                    }
                } else {
                    $return_msg = "The image is an invalid type of file. Please upload GIF, JPG and PNG files only.";
                }
            } else {
                $return_msg = $this->get_upload_error_message( $_FILES[$field_name]['error'] );
            }
        }

        return( $return_msg );
    }


    /**
     * Copy an image from a foreign server to it's appropriate destination. The name of the new file will be returned in the new_file_name
     * argument. If something went wrong an error message is returned.
     *
     * @param $image_url - the url of the image on the foreign server
     * @param $upload_path - the subdirectory where the image file should go (usually based on folder/usage )
     * @param $new_file_name - will be filled with the new file name, or left blank if something goes wrong.
     * @return string
     */
    function slurp_image( $image_url, $upload_path, &$new_file_name ) {

        $valid_types   = array( "image/jpeg", "image/gif", "image/png" );
        $return_msg    = '';

        // Did they specify an image
        if ( !empty( $image_url ) ) {

            $headers = get_headers( $image_url, 1 );

            // Did the we get the headers
            if ( !empty( $headers ) ) {

                // Did they upload one of the valid file types
                if ( in_array( $headers['Content-Type'], $valid_types ) ) {

                    $slurp_file_name = basename( $image_url );

                    // Try to preserve the origin name of the image, but add a suffix to ensure uniqueness
                    $file_name_parts    = explode( '.', $slurp_file_name );
                    $extension          = strtolower( array_pop( $file_name_parts ) );
                    $file_name          = $this->scrub_filename( $new_file_name );
                    $new_file_name      = $file_name . "." . $extension;
                    $new_file_path      = $upload_path . $new_file_name;
                    $unique_id          = 1;
                    while ( file_exists( $new_file_path ) ) {
                        $new_file_name = $file_name . "_" . $unique_id . "." . $extension;
                        $new_file_path = $upload_path . $new_file_name;
                        $unique_id++;
                    }

                    copy( $image_url, $new_file_path );
                } else {
                    $return_msg = "The image is an invalid type of file. Please upload GIF, JPG and PNG files only.";
                }
            } else {
                $return_msg = "The image headers are not OK.";
            }
        }

        return( $return_msg );
    }


    /**
     * Build a descriptive upload error message based on an error code.
     *
     * @param $error_code
     * @return string
     */
    function get_upload_error_message( $error_code ) {

        switch( $error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $max_upload = (int)(ini_get('upload_max_filesize'));
                $max_post = (int)(ini_get('post_max_size'));
                $memory_limit = (int)(ini_get('memory_limit'));
                $upload_mb = min($max_upload, $max_post, $memory_limit);
                $return_msg = "The file is too large. Please reduce it to " . $upload_mb . "MB and try again.";
                break;

            case UPLOAD_ERR_PARTIAL:
                $return_msg = "The file was only partially uploaded. Please try again.";
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $return_msg = "Something went wrong on our end. Please contact us and tell us about this problem.";
                break;

            // These are errors we don't want reported to the user.
            case UPLOAD_ERR_OK:
            case UPLOAD_ERR_NO_FILE:
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_EXTENSION:
            default:
                $return_msg = "";
                break;

        }

        return ( $return_msg );
    }


    function delete_image( $image_name ) {

        // Build up a search string for the cache dir...
        $image_parts       = explode( ".", $image_name );
        $image_ext         = strtolower( array_pop( $image_parts ) );
        $image_name_no_ext = implode( '.', $image_parts );
        $cache_search_string = $this->image_cache_path . $image_name_no_ext . "_*." . $image_ext;

        // search the cache dir for scaled files that belong to this image
        foreach ( glob( $cache_search_string ) as $filename) {
            unlink( $filename );
        }

        // Now delete the original image from the uploaded directory
        unlink( $this->image_upload_path . $image_name );
    }

    /**
     * Make the full path to the cache file, based on request args
     *
     * @param $image_name_no_ext
     * @param $image_ext
     * @param $target_width
     * @param $target_height
     * @param $crop
     * @return string
     */
    function make_cache_file_path( $image_name_no_ext, $image_ext, $target_width, $target_height, $crop ) {

        return( $this->image_cache_path . $image_name_no_ext . "_" .
            $target_width . "x" . $target_height . "x" . $crop .
            "." . $image_ext );
    }

    /**
     * Function to write to generate a thumbnail from a source image, using the provided sample settings, and write
     * it as a cache file.
     *
     * @param $image_file_path
     * @param $cache_file_path
     * @param $image_type
     * @param $target_width
     * @param $target_height
     * @param $sample_region
     */
    function create_cache_image_file( $image_file_path, $cache_file_path, $image_type, $target_width, $target_height, $sample_region  ) {

        // Read in the original image
        $orig_image = $this->handlers[$image_type]['read']( $image_file_path );

        // Create a resource for the scaled image
        $scaled_img = ImageCreateTrueColor( $target_width, $target_height );

        // Copy and scale the original image to the scaled image
        imagecopyresampled( $scaled_img, $orig_image,
            $sample_region['dst_x'], $sample_region['dst_y'],
            $sample_region['src_x'], $sample_region['src_y'],
            $sample_region['dst_w'], $sample_region['dst_h'],
            $sample_region['scr_w'], $sample_region['scr_h'] );

        // Write the cache file version of the image
        $this->handlers[$image_type]['write']( $scaled_img, $cache_file_path, $this->handlers[$image_type]['cl'] );
        imagedestroy( $orig_image );
        imagedestroy( $scaled_img );
    }

}