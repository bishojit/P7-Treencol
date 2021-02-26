$.fn.login = function login(url, options) {
    let $mainPattern = $(`
<div style="z-index: 1050; background-image: url(&quot;//tools.bikiran.com/bikimg/loading-1.gif&quot;); background-position: 50% 50%; background-size: 50px; background-repeat: no-repeat; background-color: rgba(0, 0, 0, 0.75); height: 100%; width: 100%; position: fixed; top: 0; left: 0; display: none;">
    <div id="popup" style="box-shadow: rgba(0, 0, 0, 0.5) 10px 10px 10px; position: fixed; background: rgb(255, 255, 255); max-width: 95%; min-width: 300px; min-height: 200px; left: 50%; top: 50%; transform: scale(1, 1) translate(-50%, -50%); border-radius: 5px; padding: 20px; display: none;">
        <div id="popup-title" style="padding: 1px 0 9px; border-bottom: 0 solid rgb(153, 153, 153); font-size: 18px; font-weight: bold; box-shadow: rgb(153, 153, 153) 0 2px 1px -2px; margin: 10px 10px 2px; overflow: hidden; width: 500px; max-width: calc(100% - 20px);">
            Change Password
            <i id="popup-exit" class="fas fa-times-circle fa-refresh" style="color: rgb(244, 67, 54); float: right; font-size: 25px; cursor: pointer;"></i>
        </div>
        <form id="popup-form" method="post" style="padding: 12px;">
            <div class="row mb-3">
                <div class="col-12">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control"
                           placeholder="Current Password">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control"
                           placeholder="New Password">
                </div>
                <div class="col-6">
                    <label for="re_new_password">Re-New Password</label>
                    <input type="password" name="re_new_password" id="re_new_password" class="form-control"
                           placeholder="Re-New Password">
                </div>
            </div>
            <hr>
            <button class="btn btn-success btn-boxed">Change Password</button>
        </form>
    </div>
</div>
    `);


    //--This Script Values
    let $this = $(this);

    $this.on('click', function () {
        $(document.body).append($mainPattern);

        //--Show Background Element
        $mainPattern.fadeIn(300);

        //--Show Popup Element
        $mainPattern.find('#popup').delay(100).show(200);

        //--Exit Button on click Event
        $mainPattern.find('#popup-exit').on('click', function () {
            fpExit();
        });

        //--Form Submit Event
        $mainPattern.find('#popup-form').on('submit', function () {
            $.post(url, $(this).serialize(), function (data) {
                process(data);
            }, "json");
            return false;
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