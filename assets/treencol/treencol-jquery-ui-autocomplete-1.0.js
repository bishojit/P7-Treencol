(function ($) {
    $.fn.treenAutocomplete = function (sourceUrl, options) {
        let $this = $(this);

        let strEnableFixedTitle = "";

        let fnOnFocus = function () {

        };
        let fnOnKeyUp = function () {

        };
        let fnOnSelect = function (event, ui, url) {
            $(this).ajaxPageLoad(url);
        };
        let fnLiPattern = function (ul, item) {

            return $("<li>")
                .append("<div class='autocomplete-op'><img src=\"" + item.image + "\" class=\"on-autocomplete\" alt=\"\"/> <span>" + item.title + "<br>ID: " + item.id + "</span></div>")
                .appendTo(ul);
        };
        let fnSource = function (request, response, cache, sourceUrl) {
            let term = request.term;
            if (term in cache) {
                response(cache[term]);
                return;
            }

            $this.attr("style", "background-image: url(/assets/treencol/images/loading.svg) !important; background-size: 50px !important;");
            $.post(sourceUrl, request, function (data, status, xhr) {
                cache[term] = data;
                response(data);

                $this.removeAttr("style");

            }, "json");
        };
        let cache = {};

        if (typeof options == "object" && options !== null) {
            if (typeof options.onFocus == "function") {
                fnOnFocus = options.onFocus;
            }

            if (typeof options.onKeyUp == "function") {
                fnOnKeyUp = options.onKeyUp;
            }

            if (typeof options.onSelect == "function") {
                fnOnSelect = options.onSelect;
            }

            if (typeof options.liPattern == "function") {
                fnLiPattern = options.liPattern;
            }

            if (typeof options.source == "function") {
                fnSource = options.source;
            }

            if (typeof options.enableFixedTitle == "string") {
                strEnableFixedTitle = options.enableFixedTitle;
            }
        }

        $this.on('keyup', function () {

            fnOnKeyUp();
        }).autocomplete({
            minLength: 0,
            delay: 50,
            autoFocus: true,
            source: function (request, response) {

                fnSource(request, response, cache, sourceUrl);
            },
            select: function (event, ui) {
                fnOnSelect(event, ui, sourceUrl);
                //todo: Resolve Responsive Errors

                if (strEnableFixedTitle !== "") {
                    fixedTitleAction(ui.item);
                }
                return false;
            }
        }).focus(function () {

            fnOnFocus();
            $(this).data("uiAutocomplete").search($(this).val());
        }).autocomplete("instance")._renderItem = function (ul, item) {

            return fnLiPattern(ul, item);
        };

        if (strEnableFixedTitle !== "" && $this.val()) {
            $.post(sourceUrl, {term: $this.val()}, function (data) {
                fixedTitleAction(data[0]);
            }, 'json');
        }

        function fixedTitleAction(item) {
            $this.val(item['sl']);
            $this.attr('readonly', 'readonly');

            let $objDiv = $("<div class=\"autocomplete-title\">" + item[strEnableFixedTitle] + "</div>");
            let $objExit = $("<i class=\"far fa-times-circle\"></i>");
            $("body").append($objDiv);

            let borderWidth = parseInt($this.css('borderWidth').replace("px", ""));

            $objDiv.css($this.data())
                .css({
                    'display': 'block',
                    'left': ($this.offset().left + borderWidth),
                    'top': ($this.offset().top + borderWidth),
                    'height': ($this.height()) + "px",
                    'width': ($this.width()) + "px",
                    'padding-top': $this.css('padding-top'),
                    'padding-bottom': $this.css('padding-bottom'),
                    'padding-left': $this.css('padding-left'),
                    'padding-right': $this.css('padding-right'),
                })
                .append($objExit);

            $objExit.on('click', function () {
                $(this).parent().remove();
                $this.removeAttr('readonly').val('').focus();
            });

            console.log(borderWidth);
        }

        return false;
    };
})(jQuery);


/*
*
*
*
*
*
*
*
* Government
* NGO
* INGO
* Research Organization
* Propitership
* Public Company
* Private Company
* Group of Company
*
*
*
*
*
let cache = {};
$("#search_article").autocomplete({
    minLength: 0,
    delay: 10,
    autoFocus: true,
    source: function (request, response) {
        request['image-only'] = true;
        let formData = $('#form').serializeFormJSON()['index[]'];
        let unListSl_ar = [];

        $.each(formData, function (i, item) {
            unListSl_ar[i] = item;
        });
        request['unListSl_ar'] = unListSl_ar;

        let term = request.term;
        if (term in cache) {
            response(cache[term]);
            return;
        }
        $.post("<?= mkUrl("manage/article/info/search") ?>", request, function (data, status, xhr) {
            cache[term] = data;
            response(data);
        }, "json");
    },
    select: function (event, ui) {
        //$(this).val(ui.item.id);
        //$(this).next('.add-info').trigger('click');

        $(this).ajaxPageLoad("<?= mkUrl("manage/article/lead/{sl}/add", ['sl' => "\" + ui.item.id + \""]) ?>");

        return false;
    }
}).focus(function () {
    // The following works only once.
    // $(this).trigger('keydown.autocomplete');
    // As suggested by digitalPBK, works multiple times
    // $(this).data("autocomplete").search($(this).val());
    // As noted by Jonny in his answer, with newer versions use uiAutocomplete
    $(this).data("uiAutocomplete").search($(this).val());
}).autocomplete("instance")._renderItem = function (ul, item) {
    return $("<li>")
        .append("<div class='autocomplete-op'><img src=\"" + item.image + "\" class=\"on-autocomplete\" alt=\"\"/> <span>" + item.title + "<br>ID: " + item.id + "</span></div>")
        .appendTo(ul);
};
*
*
* */


/*
let fnDelayEval = function (evalString, ms) {
    let timer = setTimeout(function () {
        eval(evalString);
    }, ms);
};*/
