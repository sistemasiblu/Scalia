(function($){

    $main_links = $('.app-nav-main-links').find('.link-lvl1');

    $.each($main_links, function(k, v){
        $(v).on('click', function(e) {
            e.preventDefault();
            $main_links.removeClass('selected');
            $(this).addClass('selected');
        });
    });

    $('.links-lvl2').find('a').on('click', function(e){
        e.preventDefault();
        $(this).siblings().removeClass('selected');
        abbreviation = $(this).addClass('selected').data('abbr');
        $(this).parents('.trigger').children('.trigger-lvl2').addClass('selected');
    });

    $('.trigger-lvl3').on('click', function(e){
        e.preventDefault(); // in case we're a link
        $(this).children('.btn-menu-minus').toggleClass('on');
        $(this).parents('.links-lvl3-wrapper').toggleClass('expand');
    });

})(jQuery)