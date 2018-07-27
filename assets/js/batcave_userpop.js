$(document).ready(function() {

    var modal = $('#ajax-modal');

    $('.user_pop').on('click', function(){

        var user_id = $(this).attr('user_id');

        setTimeout(function(){
            modal.load( '/batcave/user_account_info/' + user_id, '', function(){
                modal.modal();
            });
        }, 1000);
    });
});