$(document).ready(function(){

    // Validate
    $('#pwd_form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            confirm_email: {
                required: true,
                email: true
            }

        },
        messages: {
            email: "Please enter your email address",
            confirm_email: "Please retype your email address"
        },
        highlight: function(label) {
            $(label).closest('.control-group').addClass('error');
        },
        success: function(label) {
            label
                .text('OK!').addClass('valid')
                .closest('.control-group').addClass('success');
        }
    });

}); // end document.ready

