$(document).ready(function() {

    // ---------- Ajax load new and updated stores ---------------------------------------------------------------
    var facs_shops_more;
    function storelist_update_and_display( data ) {

        facs_shops_more.replaceWith( data );

        storelist_bind_listeners();
    }


    function storelist_bind_listeners() {

        facs_shops_more = $('#facs-load-more-shops');

        // If the user clicks "readmore"
        facs_shops_more.click(function() {

            var next_url = facs_shops_more.attr( 'data-next' );

            /* change the text on the "load more" div to reflect that we're doing an ajax fetch. */
            facs_shops_more.find( 'a' ).text('Loading more stores...');

            /* GET call the archives loading routine. */
            $.get( next_url, function ( data ) {

                /* new content has been loaded and returned as "data". Pass that along to rebind the page for more scrolling */
                storelist_update_and_display( data, false, true);
            });
            // Return false to prevent the URL from clicking
            return false;
        } )
    }

    storelist_bind_listeners();

});

