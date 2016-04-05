(function ($) {
    'use strict';

    $(function () {
        // tabs
        var $tabBoxes = $('.config'),
            $currentTab,
            $tabContent,
            $hash

        // Tabs on load
        if (window.location.hash) {
            $hash = window.location.hash;
            $tabBoxes.addClass('hidden');
            $currentTab = $($hash).toggleClass('hidden');
            $('.nav-tab').removeClass('nav-tab-active');
            $('.nav-tab[href=' + $hash + ']').addClass('nav-tab-active');
        }
        //Tabs on click
        $('.nav-tab-wrapper').on('click', 'a', function (e) {
            e.preventDefault();
            $tabContent = $(this).attr('href');
            $('.nav-tab').removeClass('nav-tab-active');
            $tabBoxes.addClass('hidden');
            $currentTab = $($tabContent).toggleClass('hidden');
            $(this).addClass('nav-tab-active');
            if (history.pushState) {
                history.pushState(null, null, $tabContent);
            }
            else {
                location.hash = $tabContent;
            }
        })

    });

})(jQuery);
