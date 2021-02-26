/*On Menu Link Click*/
let page = $("html, body");
$('.has-sub-menu > a').on('click', function () {
    page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
        page.stop();
    });

    page.animate({scrollTop: $(this).position().top}, 'slow', function () {
        page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
    });

    return false;
});

