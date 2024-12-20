jQuery(document).ready(function ($) {
    $('#generate-llm-response').on('click', function () {
        const what = $('#what').val();
        const text = $('#text').val();

        const $responseDiv = $('#llm-response');

        $('#concuaid_response').html('Generating Response...');

        $('#rotating-image').addClass('rotate');

        $.ajax({
            url: concuaid_ajax_object.ajaxurl,
            method: 'POST',
            async: false, // This makes the call synchronous
            data: {
                action: 'concuaid_generate_response',
                what: what,
                text: text
            },
            success: function (response) {

                if (response) {
                    $('#concuaid_response').html('<h4>Response:</h4>' + response);
                    $('#rotating-image').removeClass('rotate');
                } else {
                    $('#concuaid_response').html('<h4>Error: Sorry I am unable to complete the request.  Please try in a couple of minutes.</h4>');
                }
            },
            error: function () {
                error.log('error');
                $('#concuaid_response').html('<h4>An error occurred while generating the description.</h4>');
            },
        });
    });
});
