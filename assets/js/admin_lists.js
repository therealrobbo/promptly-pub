$(document).ready(function() {

    /** ------------------------- PAGER ----------------------------------------------------------------------------- */
    var pager_forms      = $( '.admin_pager_form' );
    var pager_form_links = pager_forms.find( 'a' );

    pager_form_links.click( function( e ) {

        e.preventDefault();
        var this_form = $(this).closest( 'form' );
        this_form.submit();
    });


    /** ------------------------- TABLE HEADERS --------------------------------------------------------------------- */
    var table_head_forms = $( '.admin_header_form' );
    var head_form_links = table_head_forms.find( 'a' );

    head_form_links.click( function( e ) {

        e.preventDefault();
        var this_form = $(this).closest( 'form' );
        this_form.submit();
    });

});