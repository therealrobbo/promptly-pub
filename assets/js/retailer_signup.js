$(document).ready(function(){

    // Validate

    $('#signup_form').validate({
        rules: {
            fname: {
                minlength: 2,
                required: true
            },
            lname: {
                minlength: 2,
                required: true
            },
            password: {
                minlength: 8,
                required: true
            },
            confirm_password: {
                minlength: 8,
                required: true
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                minlength: 10,
                required: true
            }
        },
        messages: {
            fname: "Please enter your first name",
            lname: "Please enter your last name",
            password: "Please choose a password",
            confirm_password: "Please retype your password",
            email: "Please enter your email address",
            phone: "Please enter your phone number including area code"
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
