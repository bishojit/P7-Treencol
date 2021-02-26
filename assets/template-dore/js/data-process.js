function process(data, options) {
    let messageType = "success";
    let messageDelay = 5000; //ms
    let successDelay = 5000; //ms
    let sound = false;

    $.each(options, function (index, op) {
        if (index === "messageType") {
            messageType = op;
        } else if (index === "messageDelay") {
            messageDelay = op;
        } else if (index === "successDelay") {
            successDelay = op;
        } else if (index === "sound") {
            sound = op;
        }
    });

    if (data.error < 1) {
        messageDelay = successDelay; //ms
    } else {
        messageType = "error";
        sound = true;
    }

    if (data.message) {
        Lobibox.notify(messageType, {
            title: 'Information',
            continueDelayOnInactiveTab: true,
            msg: data.message,
            icon: "simple-icon-info",
            delay: messageDelay,
            position: 'center bottom',
            sound: sound,
        });
    }

    if (data.do) {
        if (successDelay > 0 && data.error < 1)
            delayEval(data.do, successDelay);
        else
            eval(data.do);
    }
}


function delayEval(evalString, ms) {
    let timer = setTimeout(function () {
        eval(evalString);
    }, ms);
}