var $dore = $("body").dore();

(function ($) {
    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
})(jQuery);


/*
$('#changePassword').login('/change-password/', {
    //appendBackground: false,
});
*/

/*
$('#manageTfa').tfa('/manage-tfa/', {
    //appendBackground: false,
});*/

