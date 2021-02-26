<?php


namespace Packages\tools\develop\models;


class ThemeDevelop
{

    public function getLeftSideMenu()
    {
        return "<div class=\"kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid\" id=\"kt_aside_menu_wrapper\">
            <div id=\"kt_aside_menu\" class=\"kt-aside-menu  kt-aside-menu--dropdown\" data-ktmenu-vertical=\"1\"
                 data-ktmenu-dropdown=\"1\" data-ktmenu-scroll=\"0\">

                <ul class=\"kt-menu__nav \">
                    <li class=\"kt-menu__item \" aria-haspopup=\"true\">
                        <a href=\"/\" class=\"kt-menu__link \">
                            <i class=\"kt-menu__link-icon flaticon2-gear\"></i>
                            <span class=\"kt-menu__link-text\">Export</span>
                        </a>
                    </li>
                    
                    <li class=\"kt-menu__item  kt-menu__item--submenu\" aria-haspopup=\"true\" data-ktmenu-submenu-toggle=\"hover\">
                        <a href=\"javascript:;\"
                           class=\"kt-menu__link kt-menu__toggle\">
                            <i class=\"kt-menu__link-icon flaticon2-layers-1\"></i>
                            <span class=\"kt-menu__link-text\">Actions</span>
                        </a>
                    </li>
                    <li class=\"kt-menu__item \" aria-haspopup=\"true\">
                        <a href=\"/\" class=\"kt-menu__link \">
                            <i class=\"kt-menu__link-icon flaticon2-graph\"></i>
                            <span class=\"kt-menu__link-text\">Reports</span>
                        </a>
                    </li>
                    <li class=\"kt-menu__item  kt-menu__item--submenu\" aria-haspopup=\"true\" data-ktmenu-submenu-toggle=\"hover\">
                        <a href=\"javascript:;\" class=\"kt-menu__link kt-menu__toggle\">
                            <i class=\"kt-menu__link-icon flaticon2-drop\"></i>
                            <span class=\"kt-menu__link-text\">Config</span>
                        </a>
                    </li>
                    <li class=\"kt-menu__item \" aria-haspopup=\"true\">
                        <a href=\"/\" class=\"kt-menu__link \">
                            <i class=\"kt-menu__link-icon flaticon2-analytics-2\"></i>
                            <span class=\"kt-menu__link-text\">Console</span>
                        </a>
                    </li>
                    <li class=\"kt-menu__item \" aria-haspopup=\"true\">
                        <a href=\"/\" class=\"kt-menu__link \">
                            <i class=\"kt-menu__link-icon flaticon2-protected\"></i>
                            <span class=\"kt-menu__link-text\">System</span>
                        </a>
                    </li>
                    <li class=\"kt-menu__item \" aria-haspopup=\"true\">
                        <a href=\"/\" class=\"kt-menu__link \">
                            <i class=\"kt-menu__link-icon flaticon2-mail-1\"></i>
                            <span class=\"kt-menu__link-text\">Logs</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>";
    }
}