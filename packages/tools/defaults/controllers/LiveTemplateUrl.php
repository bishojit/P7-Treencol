<?php


namespace Packages\tools\defaults\controllers;


use Core\LiveTemplate;

class LiveTemplateUrl
{
    function liveTemplate()
    {
        $LiveTemplate = new LiveTemplate();
        return $LiveTemplate->getHtmlAsJs() . "\n" . $LiveTemplate->getJs() . "";
    }
}