$(document).ready(function(){

    var edit_account = $('#edit_account');

    function pop_edit_hash( ) {
        var hash;
        hash = window.location.hash.substring(1);
        if ( hash == 'edit_account' ) {
            edit_account.show(1);
            var newTop = edit_account.offset().top - 200;
            $(window).scrollTop( newTop );
        }
    }


    $(window).on('hashchange', function() {
        pop_edit_hash();
    });

    pop_edit_hash();

    $('#open_edit').click(function() {

        if ( edit_account.is( ":visible" ) ) {
            edit_account.hide();
        } else {
            edit_account.show();
            var newTop = edit_account.offset().top - 200;
            $(window).scrollTop( newTop );
        }
    });


    $('.poplink').popover({
        template: '<div class="popover widepop"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'

    });



    // --------------------- Validation -------------------
    $( "#edit_contact_form" ).submit(function( event ) {

        var error = 0;
        $( this ).find( '.validate' ).each( function( index ) {
            var input_len  = $( this ).val( ).length;
            var target_len = $( this).attr( 'data-valid-length' );

            if ( input_len < target_len ) {
                var error = 1;

            }
        });

        if ( error==1 ) {

            alert('Please fill out name, email and phone fields!');
            event.preventDefault();
            return false;
        } else  {
            return true;
        }
    });
});