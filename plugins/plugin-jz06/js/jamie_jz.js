jQuery(document).ready(function($) {
    $('.jamie_jz06_class').blur(function() {
        $.ajax({
            type: "POST",
            data: {
                color: $(this).val(),
                action: "jamie_color_check",
                nonce: jamie_ajax_object.nonce // 添加 nonce
            },
            url: jamie_ajax_object.ajax_url, 
            beforeSend: function() {
                $('#error_color').html('Validating...');
            },
            success: function($data) {
                if ($data == 'ok') {
                    $('#error_color').html('Input correctly');
                } else {
                    $('#error_color').html('Input cannot be empty！');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $('#error_color').html('Request failed');
            }
        });
    });
});




jQuery(document).ready(function($) {
    alert("This plugin is created by Jamie!");
    $('.description').click(function() {
        $.ajax({
            type: "POST",
            data: "description=" + $(this).text() + "&action=jamie_description",
            url: jamie_ajax_object.ajax_url,
            success: function($data) {
                if ($data != "0") {
                    $('.description').text($data);
                }
            }
        });
    });
});