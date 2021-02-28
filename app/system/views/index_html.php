<?php

use Core\HeaderMeta;

/** @var string $pageTitle */
/** @var string $pageSubTitle */
/** @var string $pageUrl */

$HeaderMeta = new HeaderMeta();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $HeaderMeta->getFullTitle() ?></title>

    <?= $HeaderMeta->getMeta() ?>
    <? //= assetsCss('develop') ?>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .welcome {
            position: fixed;
            width: 100%;
            height: 100%;
            background: #333;
        }

        #animate-me{
            width:100%;
            height:100%;
        }

        .animate-char{
color:#FFF;
        }
    </style>
</head>
<body>

<div class="welcome">
    <div id="animate-me">TREENCOL</div>
</div>

<? //= assetsJs('develop') ?>
<script>
    const area = document.querySelector('#animate-me');
    const text = area.innerHTML;
    const spannedText = text.split('').map((item, i) => {
        return `<span class="animate-char" id="text-${i}">${item}</span>`;
    });

    area.innerHTML = `<div>${spannedText.join("")}</div>`;

    console.log()
</script>
</body>
</html>