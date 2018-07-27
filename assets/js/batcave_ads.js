$(document).ready(function($){ //fire on DOM ready

    var az_page_select     = $( 'input[name=az_page]' );
    var az_platform_select = $( 'input[name=az_platform]' );
    var az_ajax_load       = $( '#ajax_load' );
    var az_list_results    = $( '#az_list_results' );
    var az_workspace       = $( '#az_workspace' );
    var az_ad_edit_submit  = $( '#az_ad_edit_submit' );
    var az_list_buttons    = $( 'li.btn' );
    var az_edit_message    = $( '#az_edit_message' );
    var az_edit_ajax       = $( '#az_edit_ajax' );
    var az_ad_edit_delete  = $( '#az_ad_edit_delete' );
    var az_action_input    = $( 'input[name=action]' );
    var az_code_input      = $( 'textarea[name=az_code]' );
    var az_form_is_dirty   = false;
    var az_form            = $( '#az_ad_edit_form' );

    function ad_zone_set_form( az_id, ad_code, zone_id, pos_id ) {

        var button_name;

        if ( zone_id == 0 ) {
            button_name = "Add Ad Zone Code";
            az_ad_edit_delete.hide();
        } else {
            button_name = "Update Ad Zone Code";
            az_ad_edit_delete.show();
        }

        $( 'input[name=az_id]' ).val( az_id );
        az_code_input.val( ad_code );
        $( 'select[name=az_zone]' ).val( zone_id );
        $( 'select[name=az_position]' ).val( pos_id );

        az_ad_edit_submit.html( button_name );
        az_edit_message.hide()
        az_form_is_dirty   = false;
    }

    function az_dirty_form_check( action_text ) {
        if ( az_form_is_dirty ) {
            var response = confirm( "You have made changes to the code in the form below. \n\n" + action_text  +
                                    " will cause your changes to be lost. Are you sure you want to do this?\n " +
                                    "\nClick OK to continue with this and lose your changes or \n" +
                                    "CANCEL to abandon this action." );
            return( response );
        }
        return( true );
    }

    function ad_zone_bind_buttons ( ) {

        az_list_buttons = $( 'li.btn' );
        az_list_buttons.click( function() {

            var az_id   = $( this ).attr( 'data-id' );
            var zone_id = $( this ).attr( 'data-zone' );
            var pos_id  = $( this ).attr( 'data-position' );
            var ad_code;

            if ( !az_dirty_form_check( ( zone_id == 0 ) ? "Starting a new ad zone" : "Loading this ad zone" ) )
                return;

            if ( zone_id == 0 ) {
                ad_code = '';
            } else {
                ad_code = $( this ).attr( 'data-code' );
            }
            az_form_is_dirty = false;

            ad_zone_set_form( az_id, ad_code, zone_id, pos_id );
        });
    }

    function ad_zone_load( ) {
        var page     = az_page_select.val();
        var platform = az_platform_select.val();

        $( 'input[name=az_id]' ).val( 0 );
        az_code_input.val( '' );
        az_page_select.val( page );
        az_platform_select.val( platform );

        if ( ( page != 0 ) && ( platform != 0 ) ) {

            // ajax load ads for this page and platform
            az_ajax_load .show();
            az_workspace.hide();

            var fetch_url = az_workspace.attr( 'data-fetch-url' );
            var embed_url = fetch_url + '/' + page + '/' + platform;
            $.get( embed_url, function ( response ) {

                az_ajax_load .hide();

                az_list_results.html( response );
                az_workspace.show();
                az_form_is_dirty   = false;

                ad_zone_bind_buttons( );
            });

            return( false );

        }
    }

    function az_ad_submit_form( ) {
        az_edit_ajax.show();

        var form_vals = az_form.serialize();

        var change_url = az_workspace.attr( 'data-change-url' );
        $.post( change_url, form_vals, function( result ){

            az_edit_ajax.hide();

            var result_parts =  result.split('::');
            if ( result_parts[0] == 0 ) {

                az_edit_message.addClass( 'alert-error' );
                az_edit_message.removeClass( 'alert-success' );
            } else {
                az_form_is_dirty = false;
                az_edit_message.addClass( 'alert-success' );
                az_edit_message.removeClass( 'alert-error' );

                az_list_results.html( result_parts[2] );
                ad_zone_bind_buttons();

                ad_zone_set_form( 0, '', 0, 0 );
            }

            az_edit_message.html( result_parts[1] );
            ad_zone_bind_undelete( ); // Just in case the return message has a undelete button
            az_edit_message.show()

        });
    }

    az_ad_edit_submit.click( function( event ) {
        event.preventDefault();
        az_action_input.val( 'update' );
        az_ad_submit_form( );
    });

    az_ad_edit_delete.click( function ( event ) {
        event.preventDefault();
        az_action_input.val( 'delete' );
        az_ad_submit_form( );
    });

    function ad_zone_bind_undelete( ) {
        $( '#ad_zone_undelete' ).click( function( event ) {
            event.preventDefault();
            var zone_id = $( '#ad_zone_undelete' ).attr( 'data-az-id' );
            $( 'input[name=az_id]' ).val( zone_id );
            az_action_input.val( 'undelete' );
            az_ad_submit_form( );
        });
    }

    az_code_input.change( function( ) {
        az_form_is_dirty = true;
    });

    ad_zone_load( );
});