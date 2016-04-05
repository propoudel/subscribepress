(function ($) {
    'use strict';
    $(document).ready(function () {

        $("#sp-subscribe-form").submit(function(e) {
            var url = $('input[name="_wp_http_referer"]').val();
            $.post(
                $(this).prop('action'),
                {
                    "sp-name": $('#sp-name').val(),
                    "sp-email": $('#sp-email').val(),
                    "_wpnonce": $('#_wpnonce').val(),
                    "_wp_http_referer": ''+ url +''
                },
                function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        $('#ajaxResponse').html(data.message).show();
                        $( '#sp-subscribe-form' ).each(function(){
                            this.reset();
                        });
                    }
                    if (data.status == "error") {
                        $('#ajaxResponse').html(data.message).show();
                    }

                },
                'json'
            ).done(function () {

            });

            e.preventDefault();
        });


    })

})(jQuery);
