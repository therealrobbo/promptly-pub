$(document).ready(function() {


    function auto_truncate( source_string, limit ) {

        var break_string = ".", pad="...";

        // return with no change if string is shorter than limit
        if( source_string.length > limit ) {

            // is break_string present between limit and the end of the string?
            if (break_string) {
                var break_stringpoint = source_string.indexOf( break_string, limit );
                if( break_stringpoint != -1 ) {
                    if( break_stringpoint < strlen( source_string ) - 1 ) {
                        source_string = substr(source_string, 0, limit) . pad;
                    }
                }
            } else {
                source_string = substr( source_string, 0, limit ) . pad;
            }
        }

        return source_string;
    }

    $( '.auto_trunc' ).each( function( index ) {

        var limit         = $( this ).attr( 'data-trunch-len' );
        var source_string = $( this ).html( );

        var trunc_string = auto_truncate( source_string, limit );
        if ( trunc_string != source_string ) {
            $( this ).html( trunc_string );
        }
    } );

});
