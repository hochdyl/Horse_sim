$(window).on("load resize ", function() {
    var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
    $('.tbl-header th:last-child').css({'padding-right':scrollWidth});
}).resize();