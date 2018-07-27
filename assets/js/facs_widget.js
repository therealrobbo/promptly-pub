$(document).ready(function($){ //fire on DOM ready

    var radius_buttons = $('#facs-form-miles').find('button');

    // Sweet, the form was submitted...
    $('#facs-form').submit(function(e){

        // Let's get the form with jQuery...
        var form = $(this);

        var zip    = form.find( input[name=zip]).val();
        var radius = form.find( input[name=radius]).val();

        window.open( form.attr('action') + '/0/' + zip + '/' + radius, "_blank" );

        // Stop the browsers default behaviour from kicking in!
        e.preventDefault()

        return false ;
    });

    radius_buttons.click(function(){
        var miles = $(this).attr('miles');

        $('#distance').val(miles);
        radius_buttons.removeClass('active');
        $(this).addClass('active');
        return false;
    });
});
