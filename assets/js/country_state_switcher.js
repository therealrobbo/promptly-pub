$(document).ready(function(){

    $('#country').change(function(){

        var new_country = $('#country').val();
        var current_state = $('#state').val();

        $.get('/retailers/store_get_states/' + new_country + '/' + current_state, function (data ) {

            /* new content has been loaded and returned as "data". Pass that along to rebind the page for more scrolling */
            $('#state').html( data );
        });


        if ( new_country == 'UK' ) {
            $('#state_controls').hide('slow');
            $('#locality').show('slow');
        } else {
            $('#state_controls').show('slow');
            $('#locality').hide('slow');
        }
    })

}); // end document.ready
