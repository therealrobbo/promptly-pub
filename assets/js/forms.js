// Create closure.
(function( $ ) {
// Plugin definition.
    $.fn.pz_validate = function( action, data ) {

        action = ( typeof action === "undefined" ) ? "bind" : action;
        data   = ( typeof data   === "undefined" ) ? ""     : data;

        if ( action == "error") {
            pz_error( $(this), data );
        } else if ( action == "bind" ) {
            pz_bind_validation( $(this), data );
        }
    };

// Private functions

    function pz_get_msg_obj( input_object ) {

        var input_id  = input_object.attr( 'id' );
        if ( typeof  input_id == "undefined" ) {
            var input_id  = input_object.attr( 'name' );
        }
        var valid_msg = $( '#valid_' + input_id );
        if ( valid_msg.length == 0 ) {
            input_object.after( "<div class='field_valid_message' id='valid_" + input_id + "'></div>" );
            valid_msg = $( '#valid_' + input_id );
        }

        return( valid_msg );
    }

    function pz_message( input_object, message_text, message_type ) {

        var valid_msg = pz_get_msg_obj( input_object );

        if ( message_text != "" ) {
            valid_msg.html( message_text );

            var valid_classes = valid_msg.attr('class').split(' ');
            for(var i = 0; i < valid_classes.length; i++) {
                if ( valid_classes[i] != 'field_valid_message' ) {
                    valid_msg.removeClass( valid_classes[i] );
                }
            }

            valid_msg.addClass( 'field_valid_' + message_type );
            valid_msg.show();
        } else {
            valid_msg.hide();
        }
    }


    function pz_has_error( input_object, validation_type ) {

        // Start with the assumption that it does not have the error type
        var has_type    = false;

        // Validation type is optional. If blank then any error returns a true
        validation_type = ( typeof validation_type === "undefined" ) ? "" : validation_type;

        // Get the error types from the input object
        var error_types = input_object.attr( 'verrors' );
        if ( ( typeof error_types != 'undefined' ) && ( error_types != '' ) ) {

            // There were errors on the object

            // Are we looking for any kind of error?
            if ( validation_type == '' ) {
                has_type = true;
            } else {
                // Looking for a specific error type so loop through the list.
                var error_list = error_types.split( ',' );
                for( var i = 0; i < error_list.length; i++ ) {
                    if ( error_list[i] == validation_type ) {
                        has_type = true;
                        break;
                    }
                }
            }
        }

        return( has_type );
    }

    function pz_add_error_type( input_object, validation_type ) {

        // If it doesn't already have the error...
        if ( !pz_has_error( input_object, validation_type) ) {

            // Add it to the current list
            var error_types = input_object.attr( 'verrors' );
            if ( ( typeof error_types != 'undefined' ) && ( error_types != '' ) ) {
                error_types = error_types + "," + validation_type;
            } else {
                error_types = validation_type;
            }
            input_object.attr( 'verrors', error_types );
        }
    }

    function pz_remove_error_type(  input_object, validation_type  ) {
        var error_types = input_object.attr( 'verrors' );
        if ( ( typeof error_types != 'undefined' ) && ( error_types != '' ) ) {
            var error_list  = error_types.split( ',' );
            var new_types   = '';
            for( var i = 0; i < error_list.length; i++ ) {
                if ( error_list[i] != validation_type ) {
                    if ( new_types != '' ) {
                        new_types += ',';
                    }
                    new_types += error_list[i];
                }
            }
            input_object.attr( 'verrors', new_types );
        }
    }

    function pz_error( input_object, message_text, validation_type ) {

        if ( message_text != '' ) {
            pz_add_error_type( input_object, validation_type );
        } else {
            pz_remove_error_type( input_object, validation_type );
        }
        if ( message_text != '' ) {
            pz_message( input_object, message_text, "error" );
            input_object.removeClass( 'field_validation_ok' );
            input_object.addClass( 'field_validation_error' );
        } else {
            if ( !pz_has_error( input_object ) ) {
                pz_message( input_object, message_text, "error" );
                input_object.addClass( 'field_validation_ok' );
                input_object.removeClass( 'field_validation_error' );
            }
        }
    }


    function pz_bind_validation( input_object, settings ) {
        switch( settings['data-type'] ) {
            case 'email':
                input_object.each( function( ) {
                    $(this).blur( function( e ) {
                        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        var email = $(this).val( );
                        email = email.trim();
                        var message = '';

                        if ( ( email != '' ) && !re.test(email) ) {
                            e.preventDefault();
                            message = 'Invalid email address';
                        }
                        pz_error( $(this), message, "email" );
                    });
                });
                break;

            case 'filled':
                input_object.each( function( ) {
                    $(this).blur( function( e ) {
                        var field_val = $(this).val( );
                        var message = '';

                        if ( field_val.trim() == '' ) {
                            e.preventDefault();
                            message = 'This field cannot be left blank';
                        }
                        pz_error( $(this), message, "filled" );
                    });
                });
                break;

            case 'submit':
                input_object.submit( function( e ) {

                    var form_message = $( '#form_valid_message' );
                    if ( form_message.length == 0 ) {
                        input_object.before( "<div class='alert' id='form_valid_message'></div>" );
                        form_message = $( '#form_valid_message' );
                    }

                    var error_fields = $( '.field_validation_error' );
                    if ( error_fields.length != 0 ) {
                        form_message.addClass( 'alert-error' );
                        form_message.html( 'Please correct the errors noted below before submitting this form' );
                        form_message.show( 'slow' );
                        e.preventDefault();
                    } else {
                        form_message.hide( 'slow' );
                    }
                });
        }
    }


// End of closure.
})( jQuery );

$(document).ready(function(){

    $( '.validate_email' ).pz_validate( "bind", {"data-type" : "email"} );
    $( '.validate_filled' ).pz_validate( "bind", {"data-type" : "filled"} );
    $( '.form-valid' ).pz_validate( "bind", { "data-type" : "submit" } );
});
