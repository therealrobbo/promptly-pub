$(document).ready(function() {

    var crop_form  = $( '#crop_form' );
    var cropbox    = $( '#cropbox' );
    var crop_ratio = cropbox.attr( 'data-aspect-ratio' );
    var orig_w     = cropbox.attr( 'data-orig-w' );
    var orig_h     = cropbox.attr( 'data-orig-h' );

    var target_w = $( 'input[name=target_w]' ).val( );
    var target_h = $( 'input[name=target_h]' ).val( );

    var action_field = $( 'input[name=action]'  );

    var cancel_button = $( '#cancel_button' );

    cropbox.Jcrop({
        onChange: showPreview,
        aspectRatio: crop_ratio,
        onSelect: showPreview,
        bgOpacity: 1
    });

    function updateCoords(c) {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    }

    function checkCoords() {
        if (parseInt($('#w').val())) return true;
        alert('Please select a crop region then press submit.');
        return false;
    }


    crop_form.submit( function( e ) {
        var action = action_field.val( );
        if ( action != 'cancel' ) {
            if ( !checkCoords() ) {
                e.preventDefault();
            }
        }
    });

    cancel_button.click( function( e ) {
        action_field.val( 'cancel' );
        crop_form.submit( );
    });

    // Our simple event handler, called from onChange and onSelect
    // event handlers, as per the Jcrop invocation above
    function showPreview( coords ) {
        if (parseInt(coords.w) > 0) {
            var rx = target_w  / coords.w;
            var ry = target_h  / coords.h;
            $('#preview').css({
                width: Math.round( rx * orig_w ) + 'px',
                height: Math.round( ry * orig_h ) + 'px',
                marginLeft: '-' + Math.round( rx * coords.x ) + 'px',
                marginTop: '-' + Math.round( ry * coords.y ) + 'px'
            });

            updateCoords( coords );
        }
    }
});
