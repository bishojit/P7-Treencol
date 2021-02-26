(function ($) {
    $.fn.ajaxFileUpload = function (url, options) {
        let $this = $(this);

        let fnOnStart = function () {

        };
        let fnOnProgress = function () {

        };
        let fnOnSuccess = function () {

        };
        let fnOnError = function () {

        };
        let fnOnChange = function () {

        };
        let fnMessage = function () {

        };
        let objDataJson = {};
        let objAllowedExts = ["jpg", "jpeg", "png", "gif"];
        let objAllowedTypes = ["image/pjpeg", "image/jpeg", "image/png", "image/x-png", "image/gif", "image/x-gif"];
        let numMaxFileSize = 5242880;  //5MB in bytes

        let fnDelayEval = function (evalString, ms) {
            let timer = setTimeout(function () {
                eval(evalString);
            }, ms);
        };

        if (typeof options == "object" && options !== null) {

            if (typeof options.onStart == "function") {
                fnOnStart = options.onStart;
            }
            if (typeof options.onProgress == "function") {
                fnOnProgress = options.onProgress;
            }
            if (typeof options.onSuccess == "function") {
                fnOnSuccess = options.onSuccess;
            }

            if (typeof options.onError == "function") {
                fnOnError = options.onError;
            }

            if (typeof options.onChange == "function") {
                fnOnChange = options.onChange;
            }

            if (typeof options.message == "function") {
                fnMessage = options.message;
            }

            if (typeof options.dataJson == "object") {
                objDataJson = options.dataJosn;
            }

            if (typeof options.allowedExts == "object") {
                objAllowedExts = options.allowedExts;
            }

            if (typeof options.allowedTypes == "object") {
                objAllowedTypes = options.allowedTypes;
            }

            if (typeof options.maxFileSize == "number") {
                numMaxFileSize = options.maxFileSize;
            }
        }

        $this.simpleUpload(url, {

            allowedExts: objAllowedExts,
            allowedTypes: objAllowedTypes,
            maxFileSize: numMaxFileSize,
            data: objDataJson,

            type: "json",
            expect: "json",

            start: function (file) { //upload started
                fnOnStart();
            },
            progress: function (progress) { //received progress
                fnOnProgress();
            },
            success: function (data) { //upload successful
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
            },
            error: function (error) { //upload failed
                fnOnError(error);

                Lobibox.notify('error', {
                    title: 'Warning',
                    continueDelayOnInactiveTab: true,
                    msg: error.message,
                    icon: "simple-icon-info",
                    delay: 5000,
                    position: 'center bottom'
                });
            }
        });

        return false;
    };
})(jQuery);