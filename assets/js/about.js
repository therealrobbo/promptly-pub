$(document).ready(function() {
    var coffee_container = $( '.container' );
    var welcome_message = $( '#word_coffee_about' );

    function size_welcome( ) {

        welcome_message.css( 'margin-top', 0 );

        var window_h = window.innerHeight;
        var body_h   = coffee_container.innerHeight();
        var coffee_h = welcome_message.height();

        var height_diff = window_h - body_h;

        welcome_message.css( 'margin-top', parseInt( height_diff / 2 ) + 'px' );
        welcome_message.css( 'margin-bottom', parseInt( height_diff / 2 ) + 'px' );
    }

    size_welcome();
    // Size coffee whenever the window size changes
    $( window ).resize(function() {
        size_welcome();
    });


});