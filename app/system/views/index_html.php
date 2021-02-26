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
    <?= assetsCss('dore') ?>
</head>
<body>

<?= assetsJs('dore') ?>

<script>

</script>

</body>
</html>