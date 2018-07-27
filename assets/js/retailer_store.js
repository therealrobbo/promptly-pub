$(document).ready(function(){
    $('#date_opened').datepicker();

    // Validate
    $('#edit_store_form').validate({
        rules: {
            name: {
                minlength: 2,
                required: true
            },
            addr1: {
                minlength: 2,
                required: true
            },
            addr2: {
                minlength: 2,
                required: false
            },
            addr3: {
                minlength: 2,
                required: false
            },
            city: {
                minlength: 2,
                required: true
            },
            zip: {
                minlength: 5,
                required: true
            },
            country: {
                required: true
            },
            phone: {
                minlength: 10,
                required: true
            }
        },
        messages: {
            name: "Please enter your store\'s name",
            addr1: "Please enter your store\'s address",
            city: "Please enter your store\'s city or town",
            zip: "Please enter your store\'s zip or postal code",
            country: "Please choose your store\'s country",
            phone: "Please enter your store\'s primary phone number"
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

    $('.poplink').popover({
        template: '<div class="popover widepop"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'

    });


});
