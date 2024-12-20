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
                if (response.success) {
                    console_log(response);
                    $('#concuaid_response').html('<h4>Response:</h4>' + response.data.message);
                    $('#rotating-image').removeClass('rotate');
                } else {
                    $('#concuaid_response').html('<h4>Error:</h4>');
                }
            },
            error: function () {
                $('#concuaid_response').html('An error occurred while generating the description.');
            },
        });
    });
});
