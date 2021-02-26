<?php


namespace Packages\treen;


class TreenModules
{
    public static function argv(array $argv, TreenProcess $TreenProcess)
    {
        if ($argv[2] == "-create") {
            $moduleName = ($argv[3]);
            self::create($moduleName, $TreenProcess);
        }
    }

    public static function create(string $moduleName, TreenProcess $TreenProcess)
    {
        $path = "app/" . $moduleName;

        if (!preg_match("/^[0-9a-zA-Z\/]+$/", $path)) {
            $TreenProcess->display("Invalid Module Name '$path'", "red");
        } else if (is_dir($path)) {
            $TreenProcess->display("'$path' module already exist", "red");
        } else {

            mkdir($path, 0777, true);
            mkdir(realpath($path) . "/controllers", 0777, true);
            mkdir(realpath($path) . "/models", 0777, true);
            mkdir(realpath($path) . "/views", 0777, true);

            copy("configs/samples/routes.xml", realpath($path) . "/routes.xml");
            $TreenProcess->display("'{$moduleName}' module Created on 'app' Directory.", "blue");
            $TreenProcess->display("Please add '<modules dir=\"{$moduleName}\" priority=\"1\" preRoutes=\"{$moduleName}\" status=\"1\" title=\"{$moduleName}\"/>' this line to 'app/app-config.xml'", "blue");
        }
    }
}