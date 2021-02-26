<?php


namespace Packages\tools\develop\controllers;


class DevelopRoute
{
    function routeList()
    {
        $route_all_ar = route()->getAllRoutesAr();
        $modules_all_ar = route()->getModulesAllAr();

        $routeGroup_all_ar = [];
        foreach ($route_all_ar as $key => $det_ar) {
            $routeGroup_all_ar[$det_ar['module']][$key] = $det_ar;
        }

        return view("developRoute_html.php", [
            'routeGroup_all_ar' => $routeGroup_all_ar,
            'modules_all_ar' => $modules_all_ar,
        ]);
    }
}