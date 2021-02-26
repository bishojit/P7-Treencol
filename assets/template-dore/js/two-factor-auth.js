$.fn.tfa = function tfa(url, options) {
    let $mainPattern = $(`
<div style="z-index: 1050; background-image: url(&quot;//tools.bikiran.com/bikimg/loading-1.gif&quot;); background-position: 50% 50%; background-size: 50px; background-repeat: no-repeat; background-color: rgba(0, 0, 0, 0.75); height: 100%; width: 100%; position: fixed; top: 0; left: 0; display: none;">
    <div id="popup" style="box-shadow: rgba(0, 0, 0, 0.5) 10px 10px 10px; position: fixed; background: rgb(255, 255, 255); max-width: 95%; min-width: 300px; min-height: 200px; left: 50%; top: 50%; transform: scale(1, 1) translate(-50%, -50%); border-radius: 5px; padding: 20px; display: none;">
        <div id="popup-title" style="padding: 1px 0 9px; border-bottom: 0 solid rgb(153, 153, 153); font-size: 18px; font-weight: bold; box-shadow: rgb(153, 153, 153) 0 2px 1px -2px; margin: 10px 10px 2px; overflow: hidden; max-width: calc(100% - 20px);">
            2FA Setup
            <i id="popup-exit" class="fas fa-times-circle fa-refresh" style="color: rgb(244, 67, 54); float: right; font-size: 25px; cursor: pointer;"></i>
        </div>
        <div id="popup-form" style="padding: 12px; max-width: 500px;">
        
            <div class="row mb-3" id="2faSmsForm">
                <div class="col-8">
                    <label for="mobile_no">Mobile No</label>
                    <div class="input-group">
                        <input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="Mobile No" value="+880">
                        <div class="input-group-append">
                            <button type="button" id="send_code" class="btn btn-primary btn-boxed">Send Code</button>
                        </div>
                    </div>
                    <small>If SMS code will not received within 120 seconds then try again.</small>
                </div>
                <div class="col-4">
                    <label for="tfa_code">Code</label>
                    <div class="input-group">
                        <input type="text" name="tfa_code" id="tfa_code" class="form-control" placeholder="2FA Code">
                        <div class="input-group-append">
                            <button type="button" id="code_verify" class="btn btn-primary btn-boxed">√</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3" id="2faSmsShow" style="display: none; max-width: 500px; width: 500px;">
                <div class="col-12">
                    <div>
                        2FA SMS Status: <b class="text-success status">Enabled</b>
                        
                        <i class="far fa-trash-alt float-right remove" style="font-size: 14px; padding: 5px; border-radius: 50px; margin: 0 10px; background: #d00000; color: #FFF; display: inline-block; width: 28px; height: 29px; text-align: center; line-height: 19px;"></i>
                        
                        <div class="custom-switch custom-switch-primary float-right">
                            <input class="custom-switch-input status-switch" id="tfa_sms_remember" type="checkbox" name="tfa_sms_remember" value="1">
                            <label class="custom-switch-btn" for="tfa_sms_remember"></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="show_mobile_no">
                        Mobile No: <b class="success">+8801925363333</b> <span>(Sense <b>2019/01/20 12:30 PM</b>)</span>
                    </div>
                </div>
            </div>
            <hr>
            <button class="btn btn-success btn-boxed done">Done</button>
        </div>
    </div>
</div>
    `);

    //--This Script Values
    $(this).on('click', function () {
        $(document.body).append($mainPattern);

        //--Show Background Element
        $mainPattern.fadeIn(300);

        //--Show Popup Element
        $mainPattern.find('#popup').delay(100).show(200);

        //--Exit Button on click Event
        $mainPattern.find('#popup-exit').on('click', function () {
            fpExit();
        });

        $mainPattern.find('#send_code').on('click', function () {
            let $tfaInpMobilNo = $mainPattern.find('#mobile_no');
            let $tfaBtnMobilNo = $(this);

            $tfaInpMobilNo.timedDisable(120000);
            $tfaBtnMobilNo.timedDisable(120000);

            $.post(url + 'sms/send-code/', {'mobile_no': $tfaInpMobilNo.val()}, function (data) {
                process(data);

                if (data.error !== 0) {
                    $tfaInpMobilNo.attr('data-stop-countdown', true);
                    $tfaBtnMobilNo.attr('data-stop-countdown', true);
                }
            }, 'json');
        });

        $mainPattern.find('#code_verify').on('click', function () {
            let $tfaInpCode = $mainPattern.find('#tfa_code');
            let $tfaBtnCode = $(this);

            $tfaBtnCode.attr('disabled', 'disabled');
            $tfaInpCode.attr('disabled', 'disabled');

            $.post(url + 'sms/code-verify/', {'tfa_code': $tfaInpCode.val()}, function (data) {
                process(data);

                if (data.error !== 0) {
                    $tfaBtnCode.removeAttr('disabled');
                    $tfaInpCode.removeAttr('disabled');
                } else {
                    fnShowStatus();
                }
            }, 'json');
        });

        //--Show Status
        let fnShowStatus = function () {
            $.post(url + 'info/', {}, function (data) {
                if (data.sms) {
                    let $statusDiv = $('#2faSmsShow').find('.status');
                    let $statusSwitch = $('#2faSmsShow').find('.status-switch');

                    if (data.sms.status === "enable") {
                        $statusDiv.text("Enabled").addClass('text-success').removeClass('text-danger');
                        $statusSwitch.prop("checked", true);
                    } else {
                        $statusDiv.text("Disabled").addClass('text-danger').removeClass('text-success');
                        $statusSwitch.prop("checked", false);
                    }

                    $('#2faSmsShow').find('.show_mobile_no').html("Mobile No: <b class=\"success\">" + data.sms.tfa_security + "</b> <span>(Sense <b>" + data.sms.time_updated_show + "</b>)</span>");

                    $('#2faSmsForm').hide();
                    $('#2faSmsShow').show();
                } else {
                    $('#2faSmsForm').show();
                    $('#2faSmsShow').hide();
                }
            }, 'json');
        };
        fnShowStatus();

        //--Update Status
        $('#2faSmsShow').find('.status-switch').on('change.bootstrapSwitch', function (e, data) {
            let $this = $(this);
            //console.log(e.target.checked);

            $.post(url + 'sms/change-status/', {status: e.target.checked}, function (data) {
                process(data);

                if (data.error !== 0) {
                    $this.prop("checked", !e.target.checked);
                } else {
                    fnShowStatus();
                }
            }, 'json');
        });


        $('.remove').confirmation({
            rootSelector: "popout",
            onConfirm: function (event, element) {
                $.post(url + 'sms/remove/', function (data) {
                    process(data);

                    if (data.error === 0) {
                        fnShowStatus();
                    }
                }, 'json');
            },
            onCancel: function (event, element) {
                //alert('cancel')
            },
            title: "Are you sure want to disable?",
            content: "2FA will disabled. It makes your account less security?",
            popout: true,
            btnOkClass: "btn btn-sm h-100 d-flex align-items-center btn-danger",
            btnCancelClass: "btn btn-sm h-100 d-flex align-items-center btn-success",
        });

        //--Exit on click Done Button
        $mainPattern.find('.done').on('click', function () {
            fpExit();
        });

        return false;
    });

    function fpExit() {
        //--Show Popup Element
        $mainPattern.find('#popup').hide(200);

        $($mainPattern).delay(100).fadeOut(300, function () {
            $mainPattern.remove();
        });
    }

    function createOption() {
        if (typeof options == "object" && options !== null) {

            /*if (typeof options.appendBackground == "boolean") {
                appendBackground = options.appendBackground;
            }*/
        }
    }

    createOption();
};


$.fn.timedDisable = function (time) {
    let $this = $(this);
    $this.attr('data-original-text', $this.html());// Remember the original text content
    $this.attr('data-stop-countdown', false);

    if (time == null) {
        time = 5000;
    }
    let seconds = Math.ceil(time / 1000);  // Calculate the number of seconds
    return $(this).each(function () {
        $(this).attr('disabled', 'disabled');
        let disabledElem = $(this);

        disabledElem.text($this.attr('data-original-text') + ' (' + seconds + ')');

        // do a set interval, using an interval of 1000 milliseconds
        //     and clear it after the number of seconds counts down to 0
        let interval = setInterval(function () {
            // decrement the seconds and update the text
            disabledElem.text($this.attr('data-original-text') + ' (' + seconds + ')');
            if (seconds === 0 || $this.attr('data-stop-countdown') === 'true') {  // once seconds is 0...
                disabledElem.removeAttr('disabled').text($this.attr('data-original-text'));   //reset to original text
                clearInterval(interval);  // clear interval
            }
            seconds--;
        }, 1000);
    });
};


$.fn.tfaCheck = function tfa(url, options) {
    options.tfa_log_sl = 0;

    let $mainPattern = $(`
<div style="z-index: 1050; background-image: url(&quot;//tools.bikiran.com/bikimg/loading-1.gif&quot;); background-position: 50% 50%; background-size: 50px; background-repeat: no-repeat; background-color: rgba(0, 0, 0, 0.75); height: 100%; width: 100%; position: fixed; top: 0; left: 0; display: none;">
    <div id="popup" style="box-shadow: rgba(0, 0, 0, 0.5) 10px 10px 10px; position: fixed; background: rgb(255, 255, 255); max-width: 95%; min-width: 300px; min-height: 200px; left: 50%; top: 50%; transform: scale(1, 1) translate(-50%, -50%); border-radius: 5px; padding: 20px; display: none;">
        <div id="popup-title" style="padding: 1px 0 9px; border-bottom: 0 solid rgb(153, 153, 153); font-size: 18px; font-weight: bold; box-shadow: rgb(153, 153, 153) 0 2px 1px -2px; margin: 10px 10px 2px; overflow: hidden; max-width: calc(100% - 20px);">
            2FA Verification
            <i id="popup-exit" class="fas fa-times-circle fa-refresh" style="color: rgb(244, 67, 54); float: right; font-size: 25px; cursor: pointer;"></i>
        </div>
        <div id="popup-form" style="padding: 12px; max-width: 500px;">
        
            <div class="row mb-3" id="2faSmsForm">
                <div class="col-12">
                
                    <div class="custom-switch custom-switch-primary float-left">
                        <input class="custom-switch-input status-switch" id="tfa_sms_remember" type="checkbox" name="tfa_sms_remember" value="1" checked>
                        <label class="custom-switch-btn" for="tfa_sms_remember"></label>
                    </div>
                    
                    <span style="font-size: 20px;margin-left: 14px;">Remember this browser for One Month.</span>
                </div>
            </div>
            <div class="row mb-3" id="2faSmsForm">
                <div class="col-8">
                    <label for="mobile_no">Write Last 4 digit of your Mobile No<br><span class="mob_num_ex_4_digit">+880192536</span>XXXX</label>
                    <div class="input-group">
                        <input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="Last 4 digit" value="">
                        <div class="input-group-append">
                            <button type="button" id="send_code" class="btn btn-primary btn-boxed">Send Code</button>
                        </div>
                    </div>
                    <small>If SMS code will not received within 120 seconds then try again.</small>
                </div>
                <div class="col-4">
                    <label for="tfa_code">&nbsp; <br>Code</label>
                    <div class="input-group">
                        <input type="text" name="tfa_code" id="tfa_code" class="form-control" placeholder="2FA Code">
                        <div class="input-group-append">
                            <button type="button" id="code_verify" class="btn btn-primary btn-boxed">√</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            <button class="btn btn-success btn-boxed done">Done</button>
        </div>
    </div>
</div>
    `);


    //--This Script Values
    $(document.body).append($mainPattern);

    //--Show Background Element
    $mainPattern.fadeIn(300);

    //--Show Popup Element
    $mainPattern.find('#popup').delay(100).show(200);

    //--Exit Button on click Event
    $mainPattern.find('#popup-exit').on('click', function () {
        fpExit();
    });

    $.post(url + 'info/' + options.user_sl + '/', function (data) {
        console.log(data);
        process(data);

        if (data.sms.tfa_security) {
            $mainPattern.find('.mob_num_ex_4_digit').html(data.sms.tfa_security);
        }
    }, 'json');


    $mainPattern.find('#send_code').on('click', function () {
        let $tfaInpMobilNo = $mainPattern.find('#mobile_no');
        let $tfaBtnMobilNo = $(this);

        $tfaInpMobilNo.timedDisable(120000);
        $tfaBtnMobilNo.timedDisable(120000);

        $.post(url + 'sms/send-code/' + options.tfa_sl, {'mobile_no_last_4': $tfaInpMobilNo.val()}, function (data) {
            process(data);

            if (data.error !== 0) {
                $tfaInpMobilNo.attr('data-stop-countdown', true);
                $tfaBtnMobilNo.attr('data-stop-countdown', true);
            }
        }, 'json');
    });

    $mainPattern.find('#code_verify').on('click', function () {
        let $tfaInpRemember = $mainPattern.find('#tfa_sms_remember');
        let $tfaInpCode = $mainPattern.find('#tfa_code');
        let $tfaBtnCode = $(this);

        options.form_login.append("<input type='hidden' class='rem' name='tfa_type' value='sms'>")
            .append("<input type='hidden' class='rem' name='tfa_code' value='" + $tfaInpCode.val() + "'>")
            .append("<input type='hidden' class='rem' name='tfa_remember' value='" + ($tfaInpRemember.is(':checked') === true ? 1 : 0) + "'>")
            .submit();
    });


    function fpExit() {
        //--Show Popup Element
        $mainPattern.find('#popup').hide(200);

        $($mainPattern).delay(100).fadeOut(300, function () {
            $mainPattern.remove();

        });

        options.form_login.find('.rem').remove();
    }

    function createOption() {
        if (typeof options == "object" && options !== null) {

            /*if (typeof options.appendBackground == "boolean") {
                appendBackground = options.appendBackground;
            }*/
        }
    }

    createOption();
};