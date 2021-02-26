<?php
/** @var string $code */
/** @var string $message */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" id="viewport" content="width=device-width, initial-scale=0.80"/>
    <meta name="viewport" id="extViewportMeta"
          content="width=device-width, initial-scale=1, maximum-scale=1.0, minimum-scale=0.50, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" id="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" id="apple-mobile-web-app-status-bar-style"
          content="black"/>

    <title>Error</title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Monoton);

        body {
            background: #33cc99;
            color: #fff;
            max-height: 700px;
            overflow-x: hidden;
        }

        .c {
            text-align: center;
            display: block;
            position: relative;
            width: 80%;
            margin: 100px auto;
        }

        ._404 {
            font-size: 100px;
            position: relative;
            display: inline-block;
            z-index: 2;
            letter-spacing: 15px;
            font-family: 'Monoton', sans-serif;
        }

        ._1 {
            text-align: center;
            display: block;
            position: relative;
            letter-spacing: 12px;
            font-size: 32px;
            line-height: 200%;
        }

        ._2 {
            text-align: center;
            display: block;
            position: relative;
            font-size: 20px;
            line-height: 200%;
        }

        .text {
            font-size: 70px;
            text-align: center;
            position: relative;
            display: inline-block;
            margin: 19px 0 0 0;
            /* top: 256.301px; */
            z-index: 3;
            width: 100%;
            line-height: 1.2em;
        }

        .btn {
            background-color: rgb(255, 255, 255);
            /* position: relative; */
            display: inline-block;
            width: 280px;
            padding: 5px;
            z-index: 5;
            font-size: 25px;
            color: #33cc99;
            text-decoration: none;
            margin: 0 10px 0 auto;
        }

        .right {
            float: right;
            width: 60%;
        }

        hr {
            padding: 0;
            border: none;
            border-top: 5px solid #fff;
            color: #fff;
            text-align: center;
            margin: 0 auto;
            width: 420px;
            height: 10px;
            z-index: -10;
        }

        hr:after {
            content: "\2022";
            display: inline-block;
            position: relative;
            top: -0.75em;
            font-size: 2em;
            padding: 0 0.2em;
            background: #33cc99;
        }

        .cloud {
            width: 350px;
            height: 120px;

            background: #FFF;
            background: linear-gradient(top, #FFF 100%, #EEE 100%);
            background: -webkit-linear-gradient(top, #FFF 100%, #EEE 100%);
            background: -moz-linear-gradient(top, #FFF 100%, #EEE 100%);
            background: -ms-linear-gradient(top, #FFF 100%, #EEE 100%);
            background: -o-linear-gradient(top, #FFF 100%, #EEE 100%);

            border-radius: 100px;
            -webkit-border-radius: 100px;
            -moz-border-radius: 100px;

            position: absolute;
            margin: 120px auto 20px;
            z-index: -1;
            transition: ease 1s;
        }

        .cloud:after, .cloud:before {
            content: '';
            position: absolute;
            background: #FFF;
            z-index: -1
        }

        .cloud:after {
            width: 100px;
            height: 100px;
            top: -50px;
            left: 50px;

            border-radius: 100px;
            -webkit-border-radius: 100px;
            -moz-border-radius: 100px;
        }

        .cloud:before {
            width: 180px;
            height: 180px;
            top: -90px;
            right: 50px;

            border-radius: 200px;
            -webkit-border-radius: 200px;
            -moz-border-radius: 200px;
        }

        .x1 {
            top: -50px;
            left: 100px;
            -webkit-transform: scale(0.3);
            -moz-transform: scale(0.3);
            transform: scale(0.3);
            opacity: 0.9;
            -webkit-animation: moveclouds 15s linear infinite;
            -moz-animation: moveclouds 15s linear infinite;
            -o-animation: moveclouds 15s linear infinite;
        }

        .x1_5 {
            top: -80px;
            left: 250px;
            -webkit-transform: scale(0.3);
            -moz-transform: scale(0.3);
            transform: scale(0.3);
            -webkit-animation: moveclouds 17s linear infinite;
            -moz-animation: moveclouds 17s linear infinite;
            -o-animation: moveclouds 17s linear infinite;
        }

        .x2 {
            left: 250px;
            top: 30px;
            -webkit-transform: scale(0.6);
            -moz-transform: scale(0.6);
            transform: scale(0.6);
            opacity: 0.6;
            -webkit-animation: moveclouds 25s linear infinite;
            -moz-animation: moveclouds 25s linear infinite;
            -o-animation: moveclouds 25s linear infinite;
        }

        .x3 {
            left: 250px;
            bottom: -70px;

            -webkit-transform: scale(0.6);
            -moz-transform: scale(0.6);
            transform: scale(0.6);
            opacity: 0.8;

            -webkit-animation: moveclouds 25s linear infinite;
            -moz-animation: moveclouds 25s linear infinite;
            -o-animation: moveclouds 25s linear infinite;
        }

        .x4 {
            left: 470px;
            botttom: 20px;

            -webkit-transform: scale(0.75);
            -moz-transform: scale(0.75);
            transform: scale(0.75);
            opacity: 0.75;

            -webkit-animation: moveclouds 18s linear infinite;
            -moz-animation: moveclouds 18s linear infinite;
            -o-animation: moveclouds 18s linear infinite;
        }

        .x5 {
            left: 200px;
            top: 300px;

            -webkit-transform: scale(0.5);
            -moz-transform: scale(0.5);
            transform: scale(0.5);
            opacity: 0.8;

            -webkit-animation: moveclouds 20s linear infinite;
            -moz-animation: moveclouds 20s linear infinite;
            -o-animation: moveclouds 20s linear infinite;
        }

        @-webkit-keyframes moveclouds {
            0% {
                margin-left: 1000px;
            }
            100% {
                margin-left: -1000px;
            }
        }

        @-moz-keyframes moveclouds {
            0% {
                margin-left: 1000px;
            }
            100% {
                margin-left: -1000px;
            }
        }

        @-o-keyframes moveclouds {
            0% {
                margin-left: 1000px;
            }
            100% {
                margin-left: -1000px;
            }
        }
    </style>
</head>
<body>

<div id="clouds">
    <div class="cloud x1"></div>
    <div class="cloud x1_5"></div>
    <div class="cloud x2"></div>
    <div class="cloud x3"></div>
    <div class="cloud x4"></div>
    <div class="cloud x5"></div>
</div>
<div class='c'>
    <div class='_404'><?php echo $code ?></div>
    <hr>
    <div class='_2'><?php echo $message ?></div>
    <a class='btn' href='/'>BACK TO HOME</a>
</div>

</body>
</html>