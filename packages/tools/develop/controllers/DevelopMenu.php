<?php


namespace Packages\tools\develop\controllers;


use Packages\mysql\QuerySelect;

class DevelopMenu
{
    function menuList()
    {
        $select = new QuerySelect("system_menu");
        $select->setQueryString("
        SELECT *
        FROM `system_menu` 
        WHERE 1
        ORDER BY `priority`
        ");
        $select->pull();
        $menu_arr_all_ar = $select->getGroupRows('parent_sl', 'sl');

        return view("developMenu_html.php", [
            'menu_arr_all_ar' => $menu_arr_all_ar,
        ]);
    }
}