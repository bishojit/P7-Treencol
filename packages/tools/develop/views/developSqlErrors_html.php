<?php

use Core\HeaderMeta;
use Packages\tools\develop\models\DevHtmlHead;
use Packages\tools\develop\models\DevHtmlSidebar;

/** @var array $errorGroup_all_ar */
/** @var array $user_all_ar */

$HeaderMeta = new HeaderMeta();

$devHtmlPHead = new DevHtmlHead();
$devHtmlPHead->setTitle("Route | Development");

$devHtmlSidebar = new DevHtmlSidebar();
$devHtmlSidebar->setActiveMenu("routes");

$true = "<span class=\"text-success\">&#10004;</span>";
$false = "<span class=\"text-danger\">&#10008;</span>";


$tr = "";
$sl = 1;

foreach ($errorGroup_all_ar as $error_all_ar) {
    $det_ar = $error_all_ar[0];
    $tr .= "<tr id=\"token_row_" . $det_ar['group_token'] . "\">
        <td class=\"text-center\"><input class=\"chk-token\" type=\"checkbox\" name=\"token[]\" value=\"" . $det_ar['group_token'] . "\" /> </td>
        <td class=\"text-center\">" . $sl . "</td>
        <td class=\"text-center\">" . $det_ar['sl'] . "</td>
        <td class=\"text-center\">
            <strong>" . date("Y-m-d", $det_ar['time_created']) . "</strong><br>
            <small>" . date("h:i:s A", $det_ar['time_created']) . "</small>
        </td>
        <td class=\"text-center\">
            " . ($det_ar['creator'] ? "
                <strong>" . $user_all_ar[$det_ar['creator']]['name'] . "</strong><br>
                " . $user_all_ar[$det_ar['creator']]['login_id'] . " 
                <small>(" . $user_all_ar[$det_ar['creator']]['sl'] . ")</small>
            " : "System Generated") . "
        </td>
        <td style=\"overflow-wrap:anywhere\">" . $det_ar['sql_string'] . "</td>
        <td>" . $det_ar['error_message'] . "</td>
        <td class=\"text-center\">
            <a href=\"#\" class=\"icon-button cus-color-blue op-error-details\" data-group-token=\"" . $det_ar['group_token'] . "\">
                <i class=\"fas fa-tv\"></i>
            </a>
        </td>
    </tr>";
    $sl++;
}


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

            <h4 class=\"panel-title\">SQL Errors</h4>
        </div>

        <div class="card-body">

            <button type="button" class="btn btn-success mb-4" id="btnAllResolve">Make as Resolved</button>

            <table class="table table-bordered table-full-width custom-style" width="100%">
                <thead>
                <tr class="text-center">
                    <th width="50">#</th>
                    <th width="50">Sl</th>
                    <th width="120">Error ID</th>
                    <th width="120">Created</th>
                    <th width="120">Creator</th>
                    <th>Query String</th>
                    <th>Error Message</th>
                    <th width="50">Action</th>
                </tr>
                </thead>
                <tbody>
                <?= $tr ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="modalLogDetails" tabindex="-1" role="dialog" aria-labelledby="modalLogDetails"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table custom-style table-sm">
                    <tbody>
                    <tr>
                        <td width="150">Error ID</td>
                        <td id="errorDetailsErrorID"></td>
                    </tr>
                    <tr>
                        <td>Query String</td>
                        <td id="errorDetailsQueryString"></td>
                    </tr>
                    <tr>
                        <td>Error Message</td>
                        <td id="errorDetailsErrorMessage"></td>
                    </tr>
                    <tr>
                        <td>Time</td>
                        <td id="errorDetailsTime"></td>
                    </tr>
                    </tbody>
                </table>

                <table class="table table-bordered custom-style table-sm">
                    <thead>
                    <tr class="text-center">
                        <th>Index</th>
                        <th>line_no</th>
                        <th>class</th>
                        <th>function</th>
                    </tr>
                    </thead>
                    <tbody id="errorDetailsErrorTbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="makeAsResolved" data-group-token="">Make as Resolved
                </button>
            </div>
        </div>
    </div>
</div>


<?= assetsJs('develop') ?>

<script>
    $('.op-error-details').on('click', function () {
        let groupToken = $(this).attr("data-group-token");
        $("#modalLogDetails").modal("show");

        $.post("<?= mkUrl('develop/sql-errors/{groupToken}/error-details', ['groupToken' => '" + groupToken + "']) ?>", function (data) {

            $("#errorDetailsErrorID").html(data[0].sl);
            $("#errorDetailsQueryString").html(data[0].sql_string);
            $("#errorDetailsErrorMessage").html(data[0].error_message);
            $("#errorDetailsTime").html(data[0].time_created);
            $("#makeAsResolved").attr('data-group-token', groupToken);

            let sl = 0;
            $("#errorDetailsErrorTbody").html("");
            $.each(data, function (index, value) {
                $("#errorDetailsErrorTbody").append(`<tr>
                    <td class='text-center' rowspan='2'>${value.index_sl}</td>
                    <td colspan='3' class='text-danger'><strong>${value.php_self}</strong> <span>(${value.line_no})</span></td>
                </tr>
                <tr>
                    <td class='text-center'>${value.line_no}</td>
                    <td>${value.class}</td>
                    <td>${value.function}</td>
                </tr>"`);
            });
        }, "json");

        return false;
    });

    $("#makeAsResolved").on('click', function () {
        let groupToken = $(this).attr("data-group-token");

        makeAsResolved(groupToken, function (data) {
            DataOperate(data, function (data) {
                location.reload();
            });
        });
    });

    $('#btnAllResolve').on('click', function () {
        let selectedTokens = [];
        let i = 0;

        $('.chk-token').each(function (i, item) {
            if ($(item).is(':checked')) {
                selectedTokens.push($(item).val());
            }
        });

        resolveSelected(selectedTokens, i);
    });

    function resolveSelected(selectedTokens, i) {

        if (selectedTokens[i]) {
            makeAsResolved(selectedTokens[i], function (data) {
                DataOperate(data, function (data) {
                    $('#token_row_' + selectedTokens[i]).addClass('table-danger');

                    resolveSelected(selectedTokens, ++i);
                });
            });
        } else {
            location.reload();
        }
    }

    function makeAsResolved(groupToken, callBack) {
        $.post("<?= mkUrl('develop/sql-errors/{groupToken}/make-resolved', ['groupToken' => '" + groupToken + "']) ?>", function (data) {
            callBack(data);
        }, "json");
    }

</script>

</body>
</html>
