<?php

use Core\HeaderMeta;
use Packages\tools\develop\models\DevHtmlHead;
use Packages\tools\develop\models\DevHtmlSidebar;

/** @var array $routeGroup_all_ar */
/** @var array $modules_all_ar */

$HeaderMeta = new HeaderMeta();

$devHtmlPHead = new DevHtmlHead();
$devHtmlPHead->setTitle("Route | Development");

$devHtmlSidebar = new DevHtmlSidebar();
$devHtmlSidebar->setActiveMenu("routes");

$true = "<span class=\"text-success\">&#10004;</span>";
$false = "<span class=\"text-danger\">&#10008;</span>";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= $HeaderMeta->getFullTitle() ?></title>
    <?= $HeaderMeta->getMeta() ?>
    <?= assetsCss('develop') ?>
</head>

<body>
<?= $devHtmlSidebar->getHtml(); ?>

<div class="container-fluid">

    <?php foreach ($modules_all_ar as $modulesObj) {
        $tr = "";
        $j = 0;
        $i = 1;

        //dd();

        $modulePriority = (string)$modulesObj['priority'];
        $moduleTitle = (string)$modulesObj['title'];
        $modulePath = (string)$modulesObj['dir'];
        $routeModule_ar = $routeGroup_all_ar[$modulePath];
        ?>
        <div class="card mt-4">
            <div class="card-header">

                <h4 class=\"panel-title\"><?= $moduleTitle . " (" . $modulePath . ") <span class=\"float-right\">Priority: " . $modulePriority . "</span>" ?></h4>
            </div>

            <div class="card-body">
                <?php
                foreach ($routeModule_ar ?: [] as $key => $det_ar) {

                    if ($det_ar['get']) {
                        if (!strpos("_" . $det_ar['route'], "{")) {
                            $url = mkUrl($det_ar['route']);
                            $tdRoute = "<td><a href=\"$url\" target='_blank'>" . $det_ar['route'] . "</a></td>";
                        } else {
                            $tdRoute = "<td>" . $det_ar['route'] . "</td>";
                        }

                        $tr .= "</tr>
                            <td class=\"text-center\">" . $i++ . "</td>
                            <td>" . $det_ar['pageTitle'] . "</td>
                            $tdRoute
                            <td class=\"text-center\">GET</td>
                            <td>" . $det_ar['controller'] . "</td>
                            <td>" . $det_ar['get'] . "</td>
                            <td class=\"text-center\">" . ${$det_ar['auth']} . "</td>
                        </tr>";
                    }

                    if ($det_ar['post']) {
                        $tr .= "</tr>
                            <td class=\"text-center\">" . $i++ . "</td>
                            <td>" . $det_ar['pageTitle'] . "</td>
                            <td>" . $det_ar['route'] . "</td>
                            <td class=\"text-center\">POST</td>
                            <td>" . $det_ar['controller'] . "</td>
                            <td>" . $det_ar['post'] . "</td>
                            <td class=\"text-center\">" . ${$det_ar['auth']} . "</td>
                    </tr>";
                    }
                }
                ?>
                <table class="table table-bordered table-full-width custom-style" width="100%">
                    <thead>
                    <tr class="text-center">
                        <th width="3%">Sl</th>
                        <th width="22%">Title</th>
                        <th width="20%">Route</th>
                        <th width="10%">Request</th>
                        <th width="20%">Controller</th>
                        <th width="20%">Method</th>
                        <th width="5%">Auth</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?= $tr ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</div>
<?= assetsJs('develop') ?>


<script>

</script>

</body>
</html>
