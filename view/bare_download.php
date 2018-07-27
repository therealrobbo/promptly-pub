<?php
    header( 'Content-Description: File Transfer' );
    header( 'Content-Type: ' . $mime_type );
    header( 'Content-disposition: attachment; filename=' . $file_name );
    header( 'Cache-Control: must-revalidate' );
    header( 'Pragma: public' );
    header( 'Content-Length: ' . strlen( $output_text ) );

    echo ( $output_text );
?>