$(document).ready(function($){ //fire on DOM ready

    $('.pc_delete_button').click(function(e) {

        var rusure = confirm("Are you sure you want to delete this partner code?");
        if (rusure==true) {
            return( true );
        } else {
            e.preventDefault();
            return( false );
        }
    });

    function pc_fill_form( values ) {

        $('#pc_form_heading').html( values['heading'] );
        $('input[name=action]').val( values['action'] );
        $('input[name=pc_id]').val( values['pc_id'] );
        $('input[name=pc_name]').val( values['pc_name'] );
        $('textarea[name=pc_code]').val( values['pc_code'] );
        $('select[name=pc_location]').val( values['pc_location'] );
        $('input[name=pc_order]').val( values['pc_order'] );
    }

    $('#pc_add_item').click(function() {

        var pc_edit_form = $('#pc_edit_form')
        var is_visible    = pc_edit_form.is(":visible");

        if ( is_visible ) {
            pc_edit_form.hide();
        } else {
            pc_fill_form({
                'heading':          'Add Partner Code',
                'action':           'add',
                'pc_id':            0,
                'pc_name':          '',
                'pc_code':          '',
                'pc_location':      1,
                'pc_order':         10000
            });
            pc_edit_form.show();
        }

    });

    function pc_update_sort() {
        var new_count  = 1;

        $('.sortable li').each(function() {

            // Set the sort value to 0 for the cover, and the curent count for other pages
            var pc_sort_input  = $(this).find('.pc_sort');
            $( pc_sort_input[0] ).val( new_count );

            var pc_location_input = $(this).find('.pc_location');
            var location_id        = $(this).parent().attr('location');
            $( pc_location_input[0]).val( location_id );

            new_count++;
        });
    }

    $(function() {
        $( "ul.sortable" ).sortable({
            accept: ".sortable li",
            placeholder: "sortable-placeholder",
            forcePlaceholderSize: true,
            distance: 1,
            revert: 200,
            tolerance: 'pointer',
            dropOnEmpty: true,
            connectWith: 'ul.sortable',
            update : function () {
                pc_update_sort();
            }
        });
        // $( ".sortable" ).disableSelection();
    });


});
