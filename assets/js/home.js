$(document).ready(function() {

    var coffee_container = $( '.container' );
    var coffee_edit      = null;
    var coffee_widgets   = $( '#writing_widgets' );

    // ------------------------------ WYSIWYG Editor
    $.trumbowyg.svgPath = '/resource/img/trumbowyg-icons.svg';

    function get_coffee_edit( ) {
        if ( coffee_edit == null ) {
            coffee_edit = $( '.trumbowyg-editor' );
        }
        return( coffee_edit );
    }

    var coffee_time       = $( '#coffee_time' );
    var coffee_dirty      = false;
    var coffee_dirty_time = 0;
    var auto_shrink       = true;
    coffee_time.trumbowyg({
        btns: [['bold', 'italic', 'underline'],'btnGrp-justify','btnGrp-lists',['removeformat'],['fullscreen']]
    })
        .on( 'tbwchange', function( ) {
            coffee_edit = get_coffee_edit();
            var char_count = coffee_edit.text().length;

            coffee_dirty = true;
            coffee_dirty_time = $.now();
            prompt_change_state( 'dirty' );
            show_download( );
            if ( ( char_count > 100 ) && auto_shrink ) {
                if ( !prompt_area.hasClass( 'prompt_hidden' ) &&
                     !prompt_area.hasClass( 'prompt_small' ) ) {
                    prompt_shrink.trigger( 'click' );
                    auto_shrink = false;
                }
            }
        });

    var coffee_control_download = $( '#coffee_control_download' );
    function show_download( ) {
        coffee_edit = get_coffee_edit();
        var char_count = coffee_edit.text().length;

        if ( char_count > 0 ) {
            coffee_control_download.show();
        } else {
            coffee_control_download.hide();
        }
    }
    function size_coffee( ) {
        coffee_edit = get_coffee_edit();

        var window_h = window.innerHeight;
        var body_h   = coffee_container.innerHeight();
        var coffee_h = coffee_edit.height();

        var height_diff = window_h - body_h;

        coffee_edit.height( ( coffee_h + height_diff ) + 'px' );
    }

    //--------------------------------- Dirty Business
    var coffee_saver_idle = true;
    var coffee_saver = setInterval( function( ){
             // Save the text...
             save_text();
    }, 20000 );

    function save_text( ) {
        // If the coffee editor is dirty and we're not already saving a previous save...
        if ( coffee_dirty && coffee_saver_idle ) {

            // Start the process...
            coffee_saver_idle = false;
            prompt_change_state( 'saving' );

            var dirty_time  = coffee_dirty_time;
            var sample_text = coffee_time.trumbowyg( 'html' );
            var prompt_id = prompt_area.attr( 'data-prompt-id' );
            $.ajax({
                type: "POST",
                url: '/session',
                data: {
                    'sample_text': sample_text,
                    'prompt_id': prompt_id
                },
                success: function( data ) {

                    if ( data.error == 0 ) {
                        if ( coffee_dirty_time == dirty_time ) {
                            coffee_dirty = false;
                            prompt_change_state('saved');
                        } else {
                            prompt_change_state('dirty');
                        }
                    } else {
                        prompt_change_state( 'error' );
                    }
                    coffee_saver_idle = true;
                },
                dataType: 'json'
            });
        }
    }

    //---------------------------------- Prompt State
    var prompt_state = $( '#prompt_state' );
    function prompt_change_state( state_flag ) {
        var current_state = prompt_state.attr( 'data-state' );

        if ( current_state != state_flag ) {
            prompt_state.attr( 'data-state', state_flag );
            prompt_state.removeClass( 'label-default' );
            prompt_state.removeClass( 'label-danger' );
            prompt_state.removeClass( 'label-warning' );
            prompt_state.removeClass( 'label-info' );
            prompt_state.removeClass( 'label-success' );
            prompt_state.removeClass( 'label-primary' );
            prompt_state.css( 'cursor', 'auto' );
            if ( state_flag == 'dirty' ) {
                prompt_state.addClass( 'label-default' );
                prompt_state.html( 'changed' );
                prompt_state.show();
                prompt_state.css( 'cursor', 'pointer' );
            } else if ( state_flag == 'saving' ) {
                prompt_state.addClass( 'label-warning' );
                prompt_state.html( 'saving...' );
                prompt_state.show();
            } else if ( state_flag == 'saved' ) {
                prompt_state.addClass( 'label-success' );
                prompt_state.html( 'saved' );
                prompt_state.show();
            } else if ( state_flag == 'error' ) {
                prompt_state.addClass( 'label-danger' );
                prompt_state.html( 'error :-(' );
                prompt_state.show();
            } else {
                prompt_state.addClass( 'label-primary' );
                prompt_state.html( 'write!' );
                prompt_state.hide();
            }
        }
    }
    prompt_state.click( function( e ) {
        save_text();
    });

    // --------------------------------------- Prompt Sizing buttons
    var prompt_area   = $( '#prompt' );

    var prompt_shrink = $( '#prompt_shrink' );
    prompt_shrink.click( function( e ) {
        prompt_area.addClass( 'prompt_small' );
        prompt_area.removeClass( 'prompt_full' );
        prompt_area.removeClass( 'prompt_hidden' );
        size_coffee();
    });

    var prompt_grow = $( '#prompt_grow' );
    prompt_grow.click( function( e ) {
        prompt_area.addClass( 'prompt_full' );
        prompt_area.removeClass( 'prompt_small' );
        prompt_area.removeClass( 'prompt_hidden' );
        size_coffee();
    });

    var prompt_prev_class = '';
    var prompt_hide = $( '#prompt_hide' );
    var prompt_unhide = $( '#coffee_conrol_view' );
    prompt_hide.click( function( e ) {
        prompt_prev_class = prompt_area.hasClass( 'prompt_full' ) ? 'prompt_full' : 'prompt_small';
        prompt_area.addClass( 'prompt_hidden' );
        prompt_area.removeClass( 'prompt_full' );
        prompt_area.removeClass( 'prompt_small' );
        prompt_unhide.show();

        size_coffee();
    });

    prompt_unhide.click( function( e ) {
        prompt_area.removeClass( 'prompt_hidden' );
        prompt_area.removeClass( 'prompt_full' );
        prompt_area.removeClass( 'prompt_small' );
        prompt_area.addClass( prompt_prev_class );
        prompt_unhide.hide();

        size_coffee();
    });


    // --------------------------------------- Downloads
    var coffee_download = $( '.coffee_download' );
    var coffee_download_form = $( '#coffee_download_form' );
    var coffee_download_form_text = $( 'input[name=sample_text]' );
    var coffee_download_form_type = $( 'input[name=type]' );
    coffee_download.click( function( e ) {

        e.preventDefault();
        var type = $(this).attr( 'data-type' );
        var sample_text = coffee_time.trumbowyg( 'html' );

        coffee_download_form_text.val( sample_text );
        coffee_download_form_type.val( type );

        coffee_download_form.submit( );
    });


    // -------------------------------------- INIT Page
    // Size coffee whenever the window size changes
    $( window ).resize(function() {
        size_coffee();
    });

    // Size coffee at the start
    size_coffee();
    show_download( );
});
