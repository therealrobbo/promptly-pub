$(document).ready(function(){

    // Validate

    $('#login_form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            pass: {
                minlength: 4,
                required: true
            },

        },
        messages: {
            email: "Please enter your email address",
            pass: "Please enter your password"
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
