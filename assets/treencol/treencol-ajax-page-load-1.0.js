(function ($) {
    $.fn.ajaxPageLoad = function (url, options) {
        let $this = $(this);

        let fnOnSuccess = function () {

        };
        let fnOnError = function () {

        };
        let fnOnClick = function () {

        };
        let fnMessage = function () {

        };

        let dataJson = {};

        let fnDelayEval = function (evalString, ms) {
            let timer = setTimeout(function () {
                eval(evalString);
            }, ms);
        };

        if (typeof options == "object" && options !== null) {

            if (typeof options.onSuccess == "function") {
                fnOnSuccess = options.onSuccess;
            }

            if (typeof options.onError == "function") {
                fnOnError = options.onError;
            }

            if (typeof options.onClick == "function") {
                fnOnSubmit = options.onClick;
            }

            if (typeof options.message == "function") {
                fnMessage = options.message;
            }

            if (typeof options.dataJson == "object") {
                dataJson = options.dataJson;

                console.log(dataJson);
            }
        }

        fnOnClick(event);

        $.post(url, dataJson, function (data) {
            if (data.error !== 0) {
                fnOnError(data);

                Lobibox.notify('error', {
                    title: 'Warning',
                    continueDelayOnInactiveTab: true,
                    msg: data.message,
                    icon: "simple-icon-info",
                    delay: 5000,
                    position: 'center bottom'
                });
            } else {
                fnOnSuccess(data);

                Lobibox.notify('success', {
                    title: 'Message',
                    continueDelayOnInactiveTab: true,
                    msg: data.message,
                    icon: "simple-icon-info",
                    delay: 1000,
                    position: 'center bottom'
                });

                fnDelayEval(data.do, 1000);
            }
        }, "json");

        return false;
    };
})(jQuery);