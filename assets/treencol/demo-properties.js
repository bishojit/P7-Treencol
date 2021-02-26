let demoUrl = location.href;
let mainUrl = demoUrl.replace("//demo.", "//").replace("//test.", "//");

$("body").append(`
<a href="#" target="_blank" id="divDemoLabel" style="position: fixed;z-index: 10000;top: 20px;background: #800;font-size: 30px;line-height: 50px;padding: 10px 50px;border-radius: 50px;color: #FFF;opacity: .8;left: 50%;transform: translate(-50%);">
DEMO
</a>
`);

$('#divDemoLabel').hover(function () {
    $(this).html("Go to Main Site").css("background-color", "#084008");
}, function () {
    $(this).html("DEMO").css("background-color", "#800");
}).attr("href", mainUrl);