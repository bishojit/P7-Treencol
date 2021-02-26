<?php


namespace Packages\tools\develop\models;


use App\system\models\ListCommon;
use Packages\mysql\QuerySelect;

class DevMenuManage
{

    private $dataAllAr = [];
    private $dataTreeAr = [];

    public $labelIconsAr = [
        1 => "<i class=\"fas fa-arrow-right\"></i>",
        2 => "<i class=\"fas fa-chevron-right\"></i>",
        3 => "<i class=\"fas fa-angle-right\"></i>",
        4 => "<i class=\"fas fa-caret-right\"></i>",
        5 => "<i class=\"fas fa-long-arrow-alt-right\"></i>",
        6 => "<i class=\"fas fa-caret-right\"></i>",
        7 => "<i class=\"fas fa-caret-right\"></i>",
        8 => "<i class=\"fas fa-caret-right\"></i>",
        9 => "<i class=\"fas fa-caret-right\"></i>",
        10 => "<i class=\"fas fa-caret-right\"></i>",
    ];

    function __construct()
    {
        $select = new QuerySelect("system_menu");
        $select->setQueryString("
        SELECT *
        FROM `system_menu` 
        WHERE 1
        ORDER BY `sl`
        ");
        $select->pull();
        $this->dataAllAr = $select->getRows('sl');

        $this->dataTreeAr = [];
        foreach ($this->dataAllAr as $det_ar) {
            $this->dataTreeAr[$det_ar['parent_sl']][$det_ar['sl']] = $det_ar;
        }
    }

    function getHtml()
    {
        return $this->htmlTableMaker(0, 1);
    }

    private function htmlTableMaker($sl, $label)
    {
        $treeRow = "";
        $tree_all_ar = $this->dataTreeAr[$sl];
        foreach ($tree_all_ar as $det_ar) {

            $treeRow .= "<li class=\"tree-row tree-label-$label\">
                <div class=\"tree-cell\">" . $this->labelIconsAr[$label] . " " . $det_ar['title'] . "</div>
                <div class=\"tree-cell\">" . $this->labelIconsAr[$label] . " " . $det_ar['routes'] . "</div>
            </li>";
        }
        return "<ul class=\"tree-tbl\">
            $treeRow
        </ul>";
    }

}