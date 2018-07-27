$(document).ready(function($){ //fire on DOM ready

    var log_type_select     = $( '#log_type_select' );
    var log_date_select     = $( '#log_select' );
    var log_fullscreen      = $( '#log-fullscreen' );
    var log_fullscreen_icon = log_fullscreen.find( 'i' );
    var log_wrapper         = $( '#log-wrapper' );
    var page_body           = $( 'body' );
    var log_window          = $( '#log_window' );
    var log_download        = $( '#log-download' );
    var log_base_url        = log_wrapper.attr( 'data-view-url' );

    log_download.click( function( e ) {
        e.preventDefault();

        var log_type = log_type_select.val();
        var log_date = log_date_select.val();

        var log_url = log_base_url + "/download/" + log_type + "/" + log_date;
        window.open( log_url, 'Download Log' );
    });


    function log_size_windows( ) {
        $(window).scrollTop( 0 );
        page_body.css('overflow', 'hidden' );

        var new_height = $(window).height();
        page_body.height( new_height );

        var new_width  = $(window).width();

        log_wrapper.height( new_height + 5 );
        log_wrapper.width( new_width + 5 );

        log_window.height( new_height - 100 );
        log_window.width( new_width - 65 );
    }

    log_fullscreen.click( function( e ) {
        e.preventDefault();

        if ( log_wrapper.hasClass( 'fullscreen' ) ) {

            log_wrapper.fadeOut( 'fast', function() {
                page_body.css( { 'overflow' : '', 'height' : '', 'width' : ''} );
                log_wrapper.css( { 'height' : '', width: '' } );
                log_window.css( { 'height' : '', width: '' } );

                log_fullscreen_icon.removeClass( "icon-resize-small" );
                log_fullscreen_icon.addClass( "icon-resize-full" );

                log_wrapper.removeClass( 'fullscreen' );
                log_wrapper.fadeIn( 'fast' );
            } );
        } else {
            log_wrapper.fadeOut( 'fast', function() {
                log_size_windows();

                log_fullscreen_icon.removeClass( "icon-resize-full" );
                log_fullscreen_icon.addClass( "icon-resize-small" );

                log_wrapper.addClass( 'fullscreen' );
                log_wrapper.fadeIn( 'fast' );

            } );
        }
    });

    $(window).resize(function() {
        clearTimeout(window.resizeEvt);
        window.resizeEvt = setTimeout(function() {

            if ( log_wrapper.hasClass( 'fullscreen' ) )
                log_size_windows( );
        }, 250);
    });


    function log_bind_viewer( ) {
        $( '.log-data-toggle' ).click(function() {
            var toggle_parent = $(this).parent();
            var closed        = toggle_parent.hasClass( 'log-data-closed' );
            if ( closed ) {
                $(this).html( "-" );
                toggle_parent.removeClass( 'log-data-closed' );
            } else {
                $(this).html( "+" );
                toggle_parent.addClass( 'log-data-closed' );
            }
        });

        $( '.log-block-toggle' ).click( function() {
            var toggle_parent = $(this).parent();
            var toggle_icon   = $(this).find( 'i' );
            var closed        = toggle_parent.hasClass( 'log-block-closed' );
            if ( closed ) {
                toggle_icon.removeClass( 'icon-chevron-down' );
                toggle_icon.addClass( 'icon-chevron-up' );
                toggle_parent.removeClass( 'log-block-closed' );
            } else {
                toggle_icon.removeClass( 'icon-chevron-up' );
                toggle_icon.addClass( 'icon-chevron-down' );
                toggle_parent.addClass( 'log-block-closed' );
            }
        });
    }

    log_bind_viewer();



    function log_fetch( use_date ) {

        var log_type = log_type_select.val();
        var log_url = log_base_url + "/display/" + log_type;

        if ( use_date && ( log_date_select.length > 0 ) ) {
            var log_date = log_date_select.val();
            log_url += '/' + log_date;
        }

        window.location.href = log_url;
    }

    log_type_select.change( function() {
        log_fetch( false );
    });
    log_date_select.change( function() {
        log_fetch( true );
    });
});