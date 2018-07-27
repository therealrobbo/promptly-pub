jQuery(function($) {

    var widget_code  = $( '#widget_code_field' );
    var button_color = $( '#button_color' );
    var builder_form = $( '#facs_builder_form' );
    var heading_color = $( '#heading_color' );
    var heading_bg    = $( '#heading_bg' );
    var preview_area  = $( '#facs_preview_area' );

    function update_widget(  ) {
        var heading_color_val = heading_color.val( ).replace( '#', '' );
        var heading_bg_val     = heading_bg.val( ).replace( '#', '' );
        var button_color_val   = button_color.val();

        var url = '/widget/fetch_code/' + heading_color_val + '/' + heading_bg_val + '/' + button_color_val;

        /* GET call the archives loading routine. */
        $.get( url, function ( data ) {

            /* new content has been loaded and returned as "data". Pass that along to rebind the page for more scrolling */
            widget_code.val( data );
            preview_area.html( data );
        });
    }

    $('#heading_color, #heading_bg').modcoder_excolor({
        border_color : '#aaaaaa',
        round_corners : false,
        shadow : false,
        background_color : '#ffffff',
        backlight : false,
        callback_on_ok : function() {
            update_widget();
        }
    });

    widget_code.click(function(){
        $(this).select();
    });

    button_color.change(function( e ) {
        update_widget();
        e.preventDefault();
    });

    $( '#facs_widget_restore' ).click(function( e ){

        heading_color.val( builder_form.attr( 'data-default-heading-color' ) );
        heading_bg.val( builder_form.attr( 'data-default-bg-color' ) );
        button_color.val( builder_form.attr( 'data-default-button-color' ) );

        update_widget();
        e.preventDefault();
    });

    widget_code.select();
});
