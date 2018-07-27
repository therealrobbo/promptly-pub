$(document).ready(function() {

    var requestPending = false;
    var bufferString = '';

    function shopFormatResult(shop) {
        var markup = "<table class='ajax-shop-result'><tr>";

        markup += "<td class='shop-title'>" + shop.text + "</td>";
        markup += "</tr></table>"
        return markup;
    }

    function shopFormatSelection(shop) {
        return shop.text;
    }

    $('.chzn-select').select2({
        placeholder: "Type the first few letters of your store name",
        minimumInputLength: 4,
        formatResult: shopFormatResult,
        formatSelection: shopFormatSelection,
        multiple: true,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: '/retailers/claim_get_stores',
            type: "POST",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term // search term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.

                return {results: data.results};
            }
        }
    });

});