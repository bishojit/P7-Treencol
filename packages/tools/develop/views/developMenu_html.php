<?php

use Core\HeaderMeta;
use Packages\tools\develop\models\DevHtmlHead;
use Packages\tools\develop\models\DevHtmlSidebar;

/** @var array $menu_arr_all_ar */


$HeaderMeta = new HeaderMeta();


$devHtmlPHead = new DevHtmlHead();
$devHtmlPHead->setTitle("DB Comparer | Development");


$devHtmlSidebar = new DevHtmlSidebar();
$devHtmlSidebar->setActiveMenu("db-compare");

$successIcon = "<span class=\"text-success\">&#10004;</span>";
$failedIcon = "<span class=\"text-danger\">&#10008;</span>";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= $HeaderMeta->getFullTitle() ?></title>

    <?= $HeaderMeta->getMeta() ?>
    <?= assetsCss('develop') ?>
    <style>
        .card .card-body {
            padding: 1.75rem;
        }

        .card-body {
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            padding: 1.25rem;
        }

        *, ::after, ::before {
            box-sizing: border-box;
        }

        table.dataTable {
            clear: both;
            margin-top: 6px !important;
            margin-bottom: 6px !important;
            max-width: none !important;
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
    </style>
</head>

<body>
<?= $devHtmlSidebar->getHtml(); ?>

<div class="container-fluid">

    <?php foreach ($menu_arr_all_ar as $key => $menuInfo_all_ar) {
        if ($key != 0) { ?>

            <div class="card mt-4">
                <div class="card-header">
                    <h4>Menu Manage (<?= $menu_arr_all_ar[0][$key]['title'] ?>)</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table-tbl1 table table-striped table-bordered table-label-center manageList dataTable no-footer">
                            <thead>
                            <tr>
                                <td rowspan="2">SL</td>
                                <td rowspan="2">Title</td>
                                <td rowspan="2">Routes</td>
                                <td rowspan="2">Priority</td>
                                <td colspan="4" style="text-align: center">Permission</td>
                            </tr>
                            <tr>
                                <td>Teacher</td>
                                <td>Employee</td>
                                <td>Finance</td>
                                <td>Operator</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sl = 1;

                            foreach ($menuInfo_all_ar as $det_ar) {
                                ?>
                                <tr>
                                    <td><?= $sl++ ?></td>
                                    <td><?= $det_ar['title'] ?></td>
                                    <td><?= $det_ar['route'] ?></td>
                                    <td><?= $det_ar['priority'] ?></td>
                                    <td>
                                        <input type="checkbox" class="custom-switch-input" name="is_income" id="is_income"
                                                   value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="custom-switch-input" name="is_income" id="is_income"
                                                   value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="custom-switch-input" name="is_income" id="is_income"
                                                   value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="custom-switch-input" name="is_income" id="is_income"
                                                   value="1">
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php }
    } ?>
</div>
<?= assetsJs('develop') ?>


<script>

</script>

</body>
</html>
