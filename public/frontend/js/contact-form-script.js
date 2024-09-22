jQuery(function ($) {
    'use strict';

    // Subscribe form
    $('#contactForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend:function(){
                $(document).find('span.error-text').text('');
            },
            success: function(response) {
                if (response.status == 400) {
                    $.each(response.error, function(prefix, val){
                        $('span.'+prefix+'_error').text(val[0]);
                    })
                }else{
                    $('#contactForm')[0].reset();
                    $('#contactSuccessMessage').show();
                    setTimeout(function(){
                        $('#contactSuccessMessage').hide();
                    }, 3000);
                }
            }
        });
    });

}(jQuery));
