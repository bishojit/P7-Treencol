<?php

use Core\HeaderMeta;
use Packages\html\DropDown;
use Packages\tools\develop\models\DevHtmlHead;
use Packages\tools\develop\models\DevHtmlSidebar;

/** @var array $configDomain_ar */
/** @var int $error */
/** @var string $message */
/** @var array $primaryDb_ar */
/** @var array $secondaryDb_ar */
/** @var array $primaryTbl_ar */
/** @var array $secondaryTbl_ar */
/** @var array $primaryColsAll_ar */
/** @var array $secondaryColsAll_ar */
/** @var array $tables_ar */
/** @var array $mismatchAllAr */

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
</head>

<body>
<?= $devHtmlSidebar->getHtml(); ?>

<div class="container-fluid">

    <div class="card mt-4">
        <div class="card-header">
            <h4>DB Comparer</h4>
        </div>

        <form class="card-body">
            <label>Select Primary & Secondary Domain</label>
            <div class="form-row">
                <div class="col-2">
                    <?php
                    $dropDown = new DropDown("primary");
                    $dropDown->setAttribute('id', 'primaryDomain');
                    $dropDown->setAttribute('class', 'form-control');
                    $dropDown->setOption("", "Primary Config Domain");
                    $dropDown->setOptionArrayS($configDomain_ar, "", true);
                    $dropDown->setDefault($_GET['primary']);
                    echo $dropDown->getHtml();
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    $dropDown = new DropDown("secondary");
                    $dropDown->setAttribute('id', 'secondaryDomain');
                    $dropDown->setAttribute('class', 'form-control');
                    $dropDown->setOption("", "Secondary Config Domain");
                    $dropDown->setOptionArrayS($configDomain_ar, "", true);
                    $dropDown->setDefault($_GET['secondary']);
                    echo $dropDown->getHtml();
                    ?>
                </div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-info">Load DB List</button>
                    <button type="button" class="btn btn-danger" id="exchange"><i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-4">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <?php
                    $dropDown = new DropDown("primary-db");
                    $dropDown->setAttribute('id', 'primaryDb');
                    $dropDown->setAttribute('class', 'form-control w-50 float-right');
                    $dropDown->setOption("", "Select Database");
                    $dropDown->setOptionArrayM($primaryDb_ar, "Database", "Database");
                    $dropDown->setDefault($_GET['primary-db']);
                    echo $dropDown->getHtml();
                    ?>
                    <h5>Primary Database Information</h5>
                </div>

                <div class="card-body">
                    <label>Total Table: <?= count($primaryTbl_ar) ?></label>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <?php
                    $dropDown = new DropDown("secondaryDb");
                    $dropDown->setAttribute('id', 'secondaryDb');
                    $dropDown->setAttribute('class', 'form-control w-50 float-right');
                    $dropDown->setOption("", "Select Class");
                    $dropDown->setOptionArrayM($secondaryDb_ar, "Database", "Database");
                    $dropDown->setDefault($_GET['secondary-db']);
                    echo $dropDown->getHtml();
                    ?>
                    <h5>Secondary Database Information</h5>
                </div>

                <div class="card-body">
                    <label>Total Table: <?= count($secondaryTbl_ar) ?></label>
                </div>
            </div>
        </div>
    </div>


    <?php foreach ($tables_ar as $tableName) {
        $tr = "";
        $j = 0;
        $i = 1;
        ?>
        <div class="card mt-4 <?= $mismatchAllAr[$tableName]?"bg-warning":"" ?>">
            <div class="card-header">

                <h4 class=\"panel-title\" id="table_<?= $tableName ?>">
                    <i class="fas fa-table"></i>
                    <?=
                    $tableName .
                    ($secondaryTbl_ar[$tableName] ? "" : " <small class=\"text-danger\">(No Remote Table)</small>") .
                    ($mismatchAllAr[$tableName] ? " <small class=\"text-danger\">(&#10008; Mismatch in " . count($mismatchAllAr[$tableName]) . " Column)</small>" : "")
                    ?>
                </h4>
            </div>

            <div class="card-body">
                <?php
                foreach ($primaryColsAll_ar[$tableName] ?: [] as $col => $det_ar) {
                    $tr .= "</tr>
                        <td class=\"text-center\">" . $i++ . "</td>
                        <td>
                            " . ($mismatchAllAr[$tableName][$col]['Field'] ? $failedIcon : $successIcon) . "
                            " . $det_ar['Field'] . "
                        </td>
                        <td>
                            " . ($mismatchAllAr[$tableName][$col]['Type'] ? $failedIcon : $successIcon) . "
                            " . $det_ar['Type'] . "
                        </td>
                        <td>
                            " . ($mismatchAllAr[$tableName][$col]['Null'] ? $failedIcon : $successIcon) . "
                            " . $det_ar['Null'] . "
                        </td>
                        <td>
                            " . ($mismatchAllAr[$tableName][$col]['Key'] ? $failedIcon : $successIcon) . "
                            " . $det_ar['Key'] . "
                        </td>
                        <td>
                            " . ($mismatchAllAr[$tableName][$col]['Default'] ? $failedIcon : $successIcon) . "
                            " . $det_ar['Default'] . "
                        </td>
                        <td>
                            " . ($mismatchAllAr[$tableName][$col]['Extra'] ? $failedIcon : $successIcon) . "
                            " . $det_ar['Extra'] . "
                        </td>
                    </tr>";
                }
                ?>
                <table class="table table-striped custom-style" width="100%">
                    <thead>
                    <tr>
                        <th class="text-center" width="50">Sl</th>
                        <th width="300">Field</th>
                        <th width="300">Type</th>
                        <th>Null</th>
                        <th>Key</th>
                        <th>Default</th>
                        <th>Extra</th>
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

<style>
    .custom-style {
        font-size: 12px;
        white-space: nowrap;
    }

    /*  span Correction  */
    .fa-product-hunt {
        font-family: "span Awesome 5 Brands", serif;
    }

    .fa-product-hunt:before {
        content: "\f288";
    }

    .fa-exchange {
        font-family: "span Awesome 5 Brands", serif;
    }

    .fa-product-hunt:before {
        content: "\f288";
    }
</style>


<script>
    /*$("#selectConnection").on("change", function () {
        location.href = "?id=" + $(this).val();
    });
    $("#primaryDb").on("change", function () {
        $("#secondaryDb").val($(this).val());
    });
    $("#showSelectedDb").on("click", function () {
        location.href = "?id=<?= $sl ?>&primary-db=" + $("#primaryDb").val() + "&secondary-db=" + $("#secondaryDb").val();
    });
    $("#JumpTo").on("change", function () {
        location.href = "#table_" + $(this).val();
    });*/


    $('#exchange').on('click', function () {
        location.href = '?primary=' + $('#secondaryDomain').val() + '&secondary=' + $('#primaryDomain').val() + '&primary-db=' + $('#secondaryDb').val() + '&secondary-db=' + $('#primaryDb').val();
    });

    $('#primaryDb, #secondaryDb').on('change', function () {
        goUrl();
    });

    function goUrl() {
        location.href = '?primary=' + $('#primaryDomain').val() + '&secondary=' + $('#secondaryDomain').val() + '&primary-db=' + $('#primaryDb').val() + '&secondary-db=' + $('#secondaryDb').val();
    }
</script>

</body>
</html>
