<?php

namespace Packages\tools\develop\controllers;

class DevelopIndex
{
    function indexOfDevelop(): string
    {
        header("Location: /develop/routes/");
        return "";
    }
}