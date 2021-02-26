<?php

namespace Core;

class Method
{
    public function __construct(Route $Route)
    {
        $controllerPath = $Route->getControllerPath();

        if (class_exists($controllerPath)) {
            $execute = new $controllerPath();

            if (!class_exists($controllerPath) || !is_object($execute)) {

                ErrorPages::Method(1, "Wrong Object");

            } else if (!method_exists($execute, $Route->getMethod())) {

                ErrorPages::Method(2, "Method not exist");
            } else {

                echo $execute->{$Route->getMethod()}();
            }
        } else {

            ErrorPages::Method(3, "Controller Class Not Valid (Method)");
        }

    }
}