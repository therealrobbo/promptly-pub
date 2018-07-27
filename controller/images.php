<?php
/**
 * Dynamic image and thumbnail service
 */
class Images extends My_Controller {

    function __construct( $request ) {
        parent::__construct( $request );

        $this->library( "image_helpers" );
    }


    /**
     * Serve the specified image to the browser as image content.
     *
     * NOTE: calling this function will terminate the script (die).
     *
     * @param $image_path
     * @param $image_mime
     */
    function serve_image( $image_path, $image_mime ) {

        ob_end_clean();
        header( 'Content-Type: ' . $image_mime );
        readfile( $image_path );

        die();
    }




    function index( $target_width = 0, $target_height = 0, $crop = 1, $image_name = '' ) {

        // Full image path
        $image_file_path = $this->image_helpers->image_upload_path . $image_name;

        // If the base file isn't even valid, then we just bomb out.
        if ( empty( $image_name ) || !is_file( $image_file_path ) ) {
            return;
        }

        // Get the type of image. If it's not a valid type then crap out.
        $image_parts = explode( ".", $image_name );
        $image_type  = strtolower( array_pop( $image_parts ) );
        $image_name_no_ext = implode( '.', $image_parts );
        if ( ( $image_type != "jpg" ) && ( $image_type != "jpeg" ) && ( $image_type != "png" ) && ( $image_type != "gif" ) )
            return;

        // Read in the original image dimensions...
        $orig_image_specs = getimagesize( $image_file_path );

        // If they left off the scale height or width, calculate suitable replacements
        if ( $target_height == 0 ) {
            if ( $target_width == 0 ) {
                $target_width  = $orig_image_specs[0];
                $target_height = $orig_image_specs[1];
            } else {
                $target_height = intval( floatval( $orig_image_specs[1] / $orig_image_specs[0] ) * $target_width );
            }
        } else {
            if ( $target_width == 0 ) {
                $target_width = intval( floatval( $orig_image_specs[0] / $orig_image_specs[1] ) * $target_height );
            }
        }

        // If they just want the original image in its original size...
        if ( ( $target_width == $orig_image_specs[0] ) && ( $target_height == $orig_image_specs[1] ) ) {

            // Just serve the image straight
            $this->serve_image( $image_file_path, $orig_image_specs['mime'] );
            return;
        }


        // We have to serve a scaled image
        // First see what the path name of the scaled image will be. Censored images get diverted to our censored placehoder
        $cache_file_path = $this->image_helpers->make_cache_file_path( $image_name_no_ext, $image_type, $target_width, $target_height, $crop );

        // Does the scaled image already exist in the cache?
        if ( is_file( $cache_file_path ) ) {

            // The scaled image already exists in the cache, so serve it up!
            $this->serve_image( $cache_file_path, $orig_image_specs['mime'] );
            return;
        }

        // So far the image they want isn't the original or an existing cache version. We have to create it.
        // First thing to do is calculate a sample region from the original image.
        $sample_region = $this->calc_sample_region( $orig_image_specs[0], $orig_image_specs[1], $target_width, $target_height, $crop );

        // Generate the thumbnail and write the cache file.
        $this->image_helpers->create_cache_image_file( $image_file_path, $cache_file_path, $image_type, $target_width, $target_height, $sample_region );

        // Now serve the image to the browser...
        $this->serve_image( $cache_file_path, $orig_image_specs['mime'] );
    }


    /**
     * Calculate the sample region of the original image based on the target width and height, and crop settings.
     *
     * @param $orig_width
     * @param $orig_height
     * @param $target_width
     * @param $target_height
     * @param $crop
     *
     * @return array - h => sample height, w => sample width, src_x => source x offset, src_y => source y offset
     *                      dst_x => destination x offset, dst_y => destination y offset
     */
    function calc_sample_region( $orig_width, $orig_height, $target_width, $target_height, $crop ) {

        // Calculate sample dimensions based on height of the original
        $scale_factor = floatval( $orig_height / $target_height );
        $sample_by_height = array( 'scr_h' => intval( $target_height * $scale_factor ), 'scr_w' => intval( $target_width * $scale_factor ) );

        // Calculate sample dimensions based on width of the original
        $scale_factor = floatval( $orig_width / $target_width );
        $sample_by_width = array( 'scr_h' => intval( $target_height * $scale_factor ), 'scr_w' => intval( $target_width * $scale_factor ) );

        // If we're cropping the sample out of the original image, then the sample range can't be bigger than
        // the original image...
        if ( $crop ) {
            if ( ( $sample_by_height['scr_h'] > $orig_height ) || ( $sample_by_height['scr_w'] > $orig_width ) ) {
                $sample_by_width['dst_x'] = $sample_by_width['dst_y'] = 0;
                $sample_by_width['dst_w'] = $target_width;
                $sample_by_width['dst_h'] = $target_height;
                $sample_by_width['src_x'] = 0;
                $sample_by_width['src_y'] = intval( floatval( $orig_height - $sample_by_width['scr_h'] ) / 2 );
                return( $sample_by_width );
            } else {
                $sample_by_height['dst_x'] = $sample_by_height['dst_y'] = 0;
                $sample_by_height['dst_w'] = $target_width;
                $sample_by_height['dst_h'] = $target_height;
                $sample_by_height['src_x'] = intval( floatval( $orig_width - $sample_by_height['scr_w'] ) / 2 );
                $sample_by_height['src_y'] = 0;
                return( $sample_by_height );
            }
        } else {
            // We're not cropping, so we simply want the most complete sample.
            if ( ( $sample_by_height['scr_h'] >= $orig_height ) && ( $sample_by_height['scr_w'] >= $orig_width ) ) {
                $scale_factor = floatval( $target_height / $orig_height );
                return( array( 'scr_h' => $orig_height, 'scr_w' => $orig_width, 'src_x' => 0, 'src_y' => 0,
                    'dst_h' => intval( $orig_height * $scale_factor ),
                    'dst_w' => intval( $orig_width * $scale_factor ),
                    'dst_x' => intval( ( $target_width - ( floatval( $orig_width * $scale_factor ) ) ) / 2 ), 'dst_y' => 0 ) );
            } else {
                $scale_factor = floatval( $target_width / $orig_width );
                return( array( 'scr_h' => $orig_height, 'scr_w' => $orig_width, 'src_x' => 0, 'src_y' => 0,
                    'dst_h' => intval( $orig_height * $scale_factor ),
                    'dst_w' => intval( $orig_width * $scale_factor ),
                    'dst_x' => 0, 'dst_y' => intval( ( $target_height - ( floatval( $orig_height * $scale_factor ) ) ) / 2 ) ) );
            }
        }
    }

}
