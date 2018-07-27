<?php
/**
 * Various utility methods for formatting and other stuff
 */


class Util {


    /**
     * Calculate a human readable date/time string (e.g. "10 minutes ago", "3 Days Ago") from a given date
     *
     * @param $timestamp - either a string date or en epoch timestamp
     *
     * @return bool|string
     */
    function human_date( $timestamp ) {

        if ( $timestamp == '0000-00-00 00:00:00' )  {
            $difference_string = "Never";
        } else {
            if ( is_string( $timestamp ) ) {
                $timestamp = strtotime( $timestamp );
            }

            // Get time difference and setup arrays
            $difference = time() - $timestamp;

            $periods = array(
                "second"  => 1,
                "minute"  => 60,
                "hour"    => 60 * 60,
                "day"     => 60 * 60 * 24,
                "week"    => 60 * 60 * 24 * 7,
                "month"   => 60 * 60 * 24 * 30,
                "max"     => 60 * 60 * 24 * 30 * 4
            );

            // Past or present
            if ( $difference >= 0 )
                $ending = "ago";
            else {
                $difference = -$difference;
                $ending = "from now";
            }
            // If there is no difference (unlikely) then the human readable string is simply NOW
            if ( !$difference )
                $difference_string = "Now";
            else {
                $difference_string = "";
                // Loop through the periods
                foreach( $periods as $name => $value ) {

                    // Is the difference in seconds greater than the value in seconds of this period
                    if ( $difference > $value ) {

                        // Is it the max period?
                        if ($name == "max") {
                            // For the max period, we're just print out the date
                            $difference_string = date( "M d, Y H:i:m a", $timestamp );
                        } else {
                            // Otherwise we're formatting as "X unit(s) ago/to go
                            $units = intval( $difference / $value );
                            $difference_string = strval( $units ) . " $name" . ( ( $units > 1 ) ? "s" : "" ) . " " . $ending;
                        }
                    } else {
                        // we're out of range so we can stop searching...
                        break;
                    }
                }
            }
        }
        return( $difference_string );

    }

    function nl2ptag( $text_block ) {
        $text_block = str_replace("\r\n","\r\n\r\n",$text_block);

        // replace 2 or more consecutive nl characters (poss. separated by whitespace)
        // with <p>...</p>
        $text_block = str_replace('<p></p>', '', '<p>'
            . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $text_block)
            . '</p>');
        return preg_replace('/<p>[ ]*<\/p>/','',$text_block);
    }

    /**
     * Scrub out nasty database characters from a block of for display as HTML
     * (Originally wrapped in the STANDARD object, breaking it out so it can be called with instantiating a Standard Object)
     *
     * @param $text_block
     * @param int $convert_line_breaks - 0 = no conversion, 2 = nl2br, 3 = replace nl with <p> tags
     *
     * @return mixed
     */
    function display_text( $text_block, $convert_line_breaks = 0 ) {

        $text_block = str_replace( "&amp;", "&", $text_block);
        $text_block = html_entity_decode( $text_block, ENT_QUOTES, 'UTF-8');

        if ($convert_line_breaks) {
            if ( $convert_line_breaks == 2 )
                $text_block = nl2br( $text_block );
            else
                $text_block = $this->nl2ptag($text_block);
        }

        return $text_block;
    }
}
?>