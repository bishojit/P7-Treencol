<?php

namespace Packages\tools\develop\models;

class DevHtmlSidebar
{
    private $activeUrl = "";
    private $menuAllAr = [
        'menu' => [
            'title' => "Menu",
            'icon' => "<i class=\"fas fa-link\"></i>",
        ],
        'routes' => [
            'title' => "Route",
            'icon' => "<i class=\"fas fa-link\"></i>",
        ],
        'manages' => [
            'title' => "Manages",
            'icon' => "<i class=\"fas fa-link\"></i>",
        ],
        'sql-errors' => [
            'title' => "Errors",
            'icon' => "<i class=\"fas fa-database\"></i>",
        ],
        'db-compare' => [
            'title' => "Comparer",
            'icon' => "<i class=\"fas fa-database\"></i>",
        ],
    ];

    function setActiveMenu($url)
    {
        $this->activeUrl = $url;
        return $this;
    }

    function getHtml()
    {
        $li = "";
        foreach ($this->menuAllAr as $url => $det_ar) {
            $li .= "<li>
                <a href=\"/develop/$url/\" " . ($url == $this->activeUrl ? "class=\"active\"" : "") . ">" . $det_ar['icon'] . "<br>" . $det_ar['title'] . "</a>
            </li>";
        }

        return "<div class=\"sidebar\">
            <a href=\"/\">
                <img src=\"//treencol.com/assets/images/logo-bikiran-squire.png\" alt=\"" . $det_ar['title'] . "\"/>
            </a>
            
            <ul class=\"sidebar-menu\">
            $li
            </ul>
        </div>";
    }
}