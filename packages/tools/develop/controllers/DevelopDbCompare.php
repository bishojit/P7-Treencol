<?php


namespace Packages\tools\develop\controllers;

use mysqli;

class DevelopDbCompare
{
    private $configDir = "configs/access/";
    private $primary = "default";
    private $secondary = "default";

    private $errorMessageAllAr = [];

    private $mismatchAllAr = [];
    private $correctionSqlAr = [];

    private $primaryTableAr = [];
    private $secondaryTableAr = [];

    private function errorMessage(int $errorCode, string $message)
    {
        $this->errorMessageAllAr = [
            'error' => $errorCode,
            'message' => $message,
        ];
    }

    private function getTables(mysqli $dbConnect, string $dbName): array
    {
        $primaryDb_ar = [];
        if ($result = $dbConnect->query("SHOW TABLES FROM `" . $dbName . "`")) {
            while ($row = $result->fetch_assoc()) {
                $primaryDb_ar[] = $row;
            }
        }
        return $primaryDb_ar;
    }

    private function getColumns(mysqli $dbConnect, $dbName, $table): array
    {
        $primaryDb_ar = [];
        if ($result = $dbConnect->query("SHOW COLUMNS FROM `" . $dbName . "`.`" . $table . "`")) {
            while ($row = $result->fetch_assoc()) {
                $primaryDb_ar[$row['Field']] = $row;
            }
        }
        return $primaryDb_ar;
    }

    private function matchingTable(string $tableName, $primaryTableColumns_ar, $secondaryTableColumns_ar)
    {
        foreach ($primaryTableColumns_ar as $col => $col_ar) {
            foreach ($col_ar as $field => $val) {
                if ($val != $secondaryTableColumns_ar[$col][$field]) {
                    $this->mismatchAllAr[$tableName][$col][$field] = [
                        'primary' => $val,
                        'secondary' => $secondaryTableColumns_ar[$col][$field]
                    ];

                    //$this->correctionSqlAr[]="";
                }
            }
        }
    }

    function dbCompare()
    {
        $primaryDb_ar = [];
        $secondaryDb_ar = [];
        $primaryTbl_ar = [];
        $secondaryTbl_ar = [];
        $primaryColsAll_ar = [];
        $secondaryColsAll_ar = [];

        if ($_GET['primary'] && is_dir($this->configDir . $_GET['primary'])) {
            $this->primary = $_GET['primary'];
        }

        if ($_GET['secondary'] && is_dir($this->configDir . $_GET['secondary'])) {
            $this->secondary = $_GET['secondary'];
        }

        //--Collect Configs
        $configDomain_ar = scandir($this->configDir);
        foreach ($configDomain_ar as $key => $item) {
            if ($item == "." || $item == ".." || !is_dir($this->configDir . $item)) {
                unset($configDomain_ar[$key]);
            }
        }

        $dbConfigPrimaryObj = xmlFileToObject($this->configDir . $this->primary . "/" . $this->primary . ".database.xml");

        $dbConfigSecondaryObj  = xmlFileToObject($this->configDir . $this->secondary . "/" . $this->secondary . ".database.xml");

        //--Connection primary
        @$primaryConnection = new mysqli((string)$dbConfigPrimaryObj->host, (string)$dbConfigPrimaryObj->username, (string)$dbConfigPrimaryObj->password);
        if ($primaryConnection->connect_errno) {
            $this->errorMessage($primaryConnection->connect_errno, "Primary Connection failed: " . $primaryConnection->connect_error);
        } else {
            $result = $primaryConnection->query("SHOW DATABASES;");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $primaryDb_ar[] = $row;
                }

                //--
                if ($_GET['primary-db']) {
                    $primaryTbl_ar = $this->getTables($primaryConnection, $_GET['primary-db']);

                    foreach ($primaryTbl_ar as $det_ar) {
                        $tableName = $det_ar['Tables_in_' . $_GET['primary-db']];
                        $this->primaryTableAr[$tableName] = $tableName;
                        $primaryColsAll_ar[$tableName] = $this->getColumns($primaryConnection, $_GET['primary-db'], $det_ar['Tables_in_' . $_GET['primary-db']]);
                    }
                }
                $this->errorMessage(0, "");
            } else {
                $this->errorMessage(1, "No DB Found On primary");
            }
        }

        //--Connection secondary
        @$secondaryConnection = new mysqli((string)$dbConfigSecondaryObj->host, (string)$dbConfigSecondaryObj->username, (string)$dbConfigSecondaryObj->password);
        if ($secondaryConnection->connect_errno) {
            $this->errorMessage($secondaryConnection->connect_errno, "Secondary Connection failed: " . $secondaryConnection->connect_error);
        } else {
            $result = $secondaryConnection->query("SHOW DATABASES;");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $secondaryDb_ar[] = $row;
                }

                //--
                if ($_GET['secondary-db']) {
                    $secondaryTbl_ar = $this->getTables($secondaryConnection, $_GET['secondary-db']);

                    foreach ($secondaryTbl_ar as $det_ar) {
                        $tableName = $det_ar['Tables_in_' . $_GET['secondary-db']];
                        $this->secondaryTableAr[$tableName] = $tableName;
                        $secondaryColsAll_ar[$tableName] = $this->getColumns($secondaryConnection, $_GET['secondary-db'], $det_ar['Tables_in_' . $_GET['secondary-db']]);
                    }
                }

                $this->errorMessage(0, "");
            } else {
                $this->errorMessage(2, "No DB Found On secondary");
            }
        }

        //--
        $tables_ar = [];
        foreach ($primaryTbl_ar as $det1_ar) {
            $key = $det1_ar['Tables_in_' . $_GET['primary-db']];
            $tables_ar[$key] = $key;

            $this->matchingTable($key, $primaryColsAll_ar[$key], $secondaryColsAll_ar[$key]);
        }

        return view("developDbComparer_html.php", [
            //'sl' => $sl,
            'configDomain_ar' => $configDomain_ar,

            'primaryDb_ar' => $primaryDb_ar,
            'secondaryDb_ar' => $secondaryDb_ar,

            'primaryTbl_ar' => $this->primaryTableAr,
            'secondaryTbl_ar' => $this->secondaryTableAr,

            'primaryColsAll_ar' => $primaryColsAll_ar,
            'secondaryColsAll_ar' => $secondaryColsAll_ar,

            'tables_ar' => $tables_ar,
            'mismatchAllAr' => $this->mismatchAllAr,
        ]);
    }
}