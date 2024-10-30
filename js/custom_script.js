jQuery(document).ready(function($)
{
    var timeout = 0;
    var timeout_time = 1000;

    $(document).on('keyup paste', '#cache_url', function(e)
    {
        var $this = $(this);

        function keyupthis()
        {
            if((e.keyCode != 37 || e.keyCode != 38 || e.keyCode != 39 || e.keyCode != 40) && $this.val().length > 2)
            {
                timeout_time = 300;
                check($this);
            }
            else {
                timeout_time = 0;
                if($this.val().length < 2)
                {
                    $('#error-icon').addClass('hide-item');
                    $('#success-icon').addClass('hide-item');
                }
            }
        }

        clearTimeout(timeout);

        timeout = setTimeout(function(){
            keyupthis();
        }, timeout_time);

    });

    function check($this)
    {
        var data = {
            'action': 'check_cache_url',
            'target_url': $this.val(),
            'security': ajax_object.ajax_nonce
        };

        $.post(ajax_object.ajax_url, data, function(response)
        {
            if(response == 'true')
            {
                $('#error-icon').addClass('hide-item');
                $('#success-icon').removeClass('hide-item');
            }
            else
            {
                $('#error-icon').removeClass('hide-item');
                $('#success-icon').addClass('hide-item');
            }
        });
    }

    if($('#cache_url').val() != '')
    {
        check($('#cache_url'));
    }

    var success_svg = '<div class="animation-ctn hide-item" style="position: absolute; left:119px; top: 0px;">' +
        '<div class="icon icon--order-success svg">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="154px" height="154px" viewBox="0 0 154 154">' +
        '<g fill="none" stroke="#22AE73" stroke-width="2">' +
        '<circle cx="77" cy="77" r="72" style="stroke-dasharray:480px, 480px; stroke-dashoffset: 960px;"></circle>' +
        '<circle id="colored" fill="#22AE73" cx="77" cy="77" r="72" style="stroke-dasharray:480px, 480px; stroke-dashoffset: 960px;"></circle>' +
        '<polyline class="st0" stroke="#fff" stroke-width="10" points="43.5,77.8 63.7,97.9 112.2,49.4 " style="stroke-dasharray:100px, 100px; stroke-dashoffset: 200px;"/>' +
        '</g>' +
        '</svg>' +
        '</div>' +
        '</div>';

    $('#wp-admin-bar-clear-site-cache').append(success_svg);

    $(document).on('click', '.clear_cache_menu_item', function(e){

        e.preventDefault();

        var data = {
            'action': 'clear_cache_ajax',
            'security': ajax_object.ajax_nonce
        };

        $.post(ajax_object.ajax_url, data, function(response){

            if(response == 'done'){

                $('.animation-ctn').removeClass('hide-item');
                $('#wp-admin-bar-ccs-clear-cache-menu').addClass('hover');

                var timeoutID = window.setTimeout(function(){
                    $('.animation-ctn').addClass('hide-item');
                    $('#wp-admin-bar-ccs-clear-cache-menu').removeClass('hover');
                }, 5000);

                var el = document.querySelector(".svg");
                var elWrapperClone = el.innerHTML;
                el.innerHTML = elWrapperClone;
            }
            else {

            }
        });
    });

    $('.first-span').parent().parent().after('<tr id="first_tr" class="hide-item"><th class="single-th"></th><td><span class="">For more info on setting up a clear cache rule and setting up the path please visit our <a href="https://clustercs.com/kb/article/speed-optimizations/actions/speed-engine-clear-cache/" target="_blank" >knowledge base</a>.</span></td></tr>');
    $('.first-span').parent().parent().after('<tr id="second_tr" class="hide-item"><th class="single-th"></th><td><span class="">For more info on setting up NGINX cache on WordPress please visit our <a href="https://clustercs.com/kb/article/speed-optimizations/actions/caching-on-wordpress-using-nginx/" target="_blank" >knowledge base</a>.</span></td></tr>');

    $('.second-span').parent().parent().after('<tr id="third_tr" class="hide-item"><th class="single-th"></th><td><span class="">If "Yes" is checked, on add/remove page/post the whole website cache will be cleared. If "No" is checked, you will be able to manually delete the whole website cache from the plugin menu.</span></td></tr>');

    $('.info-enable-cache').on('click', function () {

        $(this).toggleClass('info-enable-cache-clicked');

        if($(this).hasClass('first-span') == 1)
        {
            $('#first_tr').toggleClass('hide-item');
            $('#second_tr').toggleClass('hide-item');
        }
        else
        {
            $('#third_tr').toggleClass('hide-item');
        }
    });

    if(ajax_object.clear_error != '')
    {
        var message_err = '';

        if(ajax_object.clear_error == 1)
        {
            message_err = 'An error occured with the ClusterCS cache clearing';
        }
        else if (ajax_object.clear_error == 2)
        {
            message_err = 'An error occured with the ClusterCS cache clearing. Consider checking the URL.';
        }

        var error_notification = '<div class="error settings-error notice is-dismissible">' +
            '<p><strong>' + message_err + '</strong></p>' +
            '<button type="button" id="hide_error_notice" class="notice-dismiss"></button>' +
            '<span class="screen-reader-text">Dismiss this notice.</span>'+
            '</div>';

        $('.wrap').find('h1').after(error_notification);
    }

    if(ajax_object.url_cc == '')
    {
        var error_notification = '<div class="error settings-error notice is-dismissible">' +
            '<p><strong>Please enter the path from the ClusterCS clear cache rule.</strong></p>' +
            '<button type="button" id="hide_error_notice" class="notice-dismiss"></button>' +
            '<span class="screen-reader-text">Dismiss this notice.</span>'+
            '</div>';

        $('.wrap').find('h1').after(error_notification);
    }

    $('#hide_error_notice').on('click', function(){
        $(this).parent().hide('slow');
    })

    $(document).on('click', '#wpbody', function(e){
        var menu = $('#wp-admin-bar-ccs-clear-cache-menu');

        if(menu.hasClass('hover'))
        {
            menu.removeClass('hover');
        }
    });

    $(document).on('hover', '#wp-admin-bar-ccs-clear-cache-menu', function(e){

        if(!$(this).hasClass('hover'))
        {
            $(this).addClass('hover');
        }
    });

    var no_cache_clear = $('#wp-admin-bar-clear-site-cache');

    if(!no_cache_clear.hasClass('clear_cache_menu_item'))
    {
        no_cache_clear.find('.ab-item').addClass('not_available')
    }

});
