(function ($) {
    $.fn.ajaxFormOnSubmit = function (options) {
        let $this = $(this);

        let fnOnSuccess = function () {

        };
        let fnOnError = function () {

        };
        let fnOnSubmit = function () {

        };
        let fnMessage = function () {

        };

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

            if (typeof options.onSubmit == "function") {
                fnOnSubmit = options.onSubmit;
            }

            if (typeof options.message == "function") {
                fnMessage = options.message;
            }
        }

        $this.on('submit', function (event) {
            let url = $this.attr('action');
            let $submitButton = $this.find('button[type=submit]');
            $submitButton.attr("disabled", "disabled");

            fnOnSubmit(event);
            $.post(url, $this.serialize(), function (data) {
                if (data.error !== 0) {
                    $submitButton.removeAttr("disabled");
                    fnOnError(data);

                    //--Autofocus of Error Field
                    if (data.fieldname) {
                        $this.find('[name=' + data.fieldname + ']').focus();
                    }

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
        });
    };
})(jQuery);