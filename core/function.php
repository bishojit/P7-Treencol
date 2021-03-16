<?php

use Core\ErrorPages;
use Core\Route;


//--Testing Functions
function dd($in): void  // Display & Die
{
    print_r($in);
    exit();
}

function dc($in, bool $newLine = true): void  // Display & Continue
{
    print_r($in);
    if ($newLine == true) {
        print_r("\n");
    }
}

function sfd($in): void  // Save on file and die
{
    file_put_contents("df.txt", json_encode($in));
    exit();
}

function sfc($in): void  // Save on file, display an continue
{
    file_put_contents("df.txt", json_encode($in));
}

function route(): Route
{
    global $Route;
    return $Route;
}

function mkUrl(string $uriRoute, array $valuesAr = [], array $getsAr = [], bool $permittedOnly = false): string
{
    global $Route, $AppInit;
    $userIndex = $AppInit->getUserIndex();
    return $Route->mkUrl($userIndex, $uriRoute, $valuesAr, $getsAr, $permittedOnly);
}

function mkFullUrl(string $uriRoute, array $valuesAr = [], array $getsAr = [], bool $permittedOnly = false): string
{
    global $Route, $AppInit;
    $userIndex = $AppInit->getUserIndex();
    $uri = $Route->mkUrl($userIndex, $uriRoute, $valuesAr, $getsAr, $permittedOnly);
    return $uri == "#" ? "#" : $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $uri;
}

function mkUrlWithIndex(int $userIndex, string $uriRoute, array $valuesAr = [], array $getsAr = [], bool $permittedOnly = false): string
{
    global $Route;
    return $Route->mkUrl($userIndex, $uriRoute, $valuesAr, $getsAr, $permittedOnly);
}

function getDefaultDomain(): string
{
    global $AppInit;
    return $AppInit->getDefaultDomain();
}

function getUserSl(): int
{
    global $Auth;
    return $Auth ? (int)$Auth->getUserSl() : 0;
}

function getUserInfoAr(): array
{
    global $Auth;
    return $Auth ? $Auth->getUserInfoAr() : [];
}

function hasUserPermission(string $permission): bool
{
    global $Auth;

    if (!$permission) {
        return true;
    }

    if ($Auth === null) {
        return false;
    }

    return !!$Auth->getDetectedPermissionAr()[$permission];
}

function getSoftInfo(): SimpleXMLElement
{
    global $SoftInfo;
    return $SoftInfo->getData();
}

function getRow(string $table, int $sl): array
{
    global $pdo;
    $qOut = $pdo->query("SELECT * " . " FROM `" . $table . "`  WHERE `sl` = " . $pdo->quote($sl));

    return $qOut->fetch() ?: [];
}

//--Template View
function view(string $layoutPath, array $val = []): string
{
    global $Route;
    $layoutFullPath = "";

    if ($Route != null) {
        $pageTitle = $Route->getUriRouteInfoAr()['pageTitle'];
        $pageSubTitle = $Route->getUriRouteInfoAr()['pageSubTitle'];
        $pageUrl = $_SERVER['REQUEST_URI'];
    }

    if (substr($layoutPath, 0, 1) != "/") {
        $layoutFullPath = "app/" . $Route->getUriRouteInfoAr()['module'] . "/views/" . $layoutPath;
    } else {
        $layoutFullPath = substr($layoutPath, 1);
    }

    $realPath = treen_realpath($layoutFullPath);
    if (!is_file($realPath)) {
        echo "Error on View File ($layoutPath)";
        exit();
    }

    foreach ($val as $key => $v) {
        $$key = $v;
    }
    unset($val);

    ob_start();
    include_once($realPath);
    $out = ob_get_clean();
    return $out;
}

function assetsCss(string $template): string
{
    $cssLink = "";
    $assetConfigObj = xmlFileToObject("configs/assets/$template.assets.xml", "Asset config file not found.");

    foreach ($assetConfigObj->group ?: [] as $groupPath) {
        foreach ($groupPath->css ?: [] as $cssPath) {
            if (substr($cssPath, -4) == "rand") {
                $cssLink .= "\n<link href=\"$cssPath=" . rand(100000, 999999) . "\" rel=\"stylesheet\" type=\"text/css\"/>";
            } else {
                $cssLink .= "\n<link href=\"$cssPath\" rel=\"stylesheet\" type=\"text/css\"/>";
            }
        }
    }

    foreach ($assetConfigObj->styles->css ?: [] as $cssPath) {
        if (substr($cssPath, -4) == "rand") {
            $cssLink .= "\n<link href=\"$cssPath=" . rand(100000, 999999) . "\" rel=\"stylesheet\" type=\"text/css\"/>";
        } else {
            $cssLink .= "\n<link href=\"$cssPath\" rel=\"stylesheet\" type=\"text/css\"/>";
        }
    }

    return $cssLink;
}

function assetsJs(string $template): string
{
    $jsLink = "";
    $assetConfigObj = xmlFileToObject("configs/assets/$template.assets.xml", "Asset config file not found.");

    foreach ($assetConfigObj->group ?: [] as $groupPath) {
        foreach ($groupPath->js ?: [] as $jsPath) {
            if (substr($jsPath, -4) == "rand") {
                $jsLink .= "\n<script src=\"$jsPath=" . rand(100000, 999999) . "\" type=\"text/javascript\"></script>";
            } else {
                $jsLink .= "\n<script src=\"$jsPath\" type=\"text/javascript\"></script>";
            }
        }
    }

    foreach ($assetConfigObj->scripts->js ?: [] as $jsPath) {
        if (substr($jsPath, -4) == "rand") {
            $jsLink .= "\n<script src=\"$jsPath=" . rand(100000, 999999) . "\" type=\"text/javascript\"></script>";
        } else {
            $jsLink .= "\n<script src=\"$jsPath\" type=\"text/javascript\"></script>";
        }
    }

    global $AppInit;
    if ($AppInit->getDefaultDomain() == "demo") {
        $jsLink .= "\n<script src=\"/assets/treencol/demo-properties.js\" type=\"text/javascript\"></script>";
    }

    return $jsLink;
}

function pdo(): PDO // do not set any type
{
    global $pdo;
    return $pdo;
}

function quote($val): string
{
    global $pdo;
    return $pdo->quote($val);
}

function quoteForLike(string $pattern, string $val): string //Pattern "%{val}"
{
    global $pdo;
    return "'" . str_replace("{val}", substr($pdo->quote($val), 1, -1), $pattern) . "'";
}

function quoteForIn(string $col, array $data_ar): string
{
    global $pdo;

    foreach ($data_ar as $key => $val) {
        $data_ar[$key] = $pdo->quote($val);
    }

    if (empty($data_ar))
        $data_ar[] = 0;

    return "`$col` IN(" . implode(", ", $data_ar) . ")";
}

function quoteForNotIn(string $col, array $data_ar): string
{
    global $pdo;

    foreach ($data_ar as $key => $val) {
        $data_ar[$key] = $pdo->quote($val);
    }

    if (empty($data_ar))
        $data_ar[] = 0;

    return "`$col` NOT IN(" . implode(", ", $data_ar) . ")";
}

function runSql(string $sql): PDOStatement
{
    global $pdo;
    $qOut = $pdo->query($sql);
    return $qOut;
}

function getTime(): int
{
    global $TimeZone;
    return $TimeZone->getTime();
}

function getIpLong(): float
{
    global $AppInit;
    return $AppInit ? $AppInit->getIpLong() : ip2long($_SERVER['REMOTE_ADDR']);
}

function treen_realpath($path, $separator = "/"): string
{
    $path_ar = explode($separator, $path);
    $newPath_ar = $path_ar;
    foreach ($path_ar as $key => $path) {
        if ($path == "..") {
            unset($newPath_ar[$key], $newPath_ar[$key - 1]);
        }
    }
    return implode($separator, $newPath_ar);
}

function xmlFileToObject($fileLocation, $fileMissingMessage = null): \SimpleXMLElement
{
    if (is_file($fileLocation)) {
        $xmlMappingXml = file_get_contents($fileLocation);
        $xmlMappingObj = simplexml_load_string($xmlMappingXml);
    } else {
        if ($fileMissingMessage == null) {
            $xmlMappingObj = simplexml_load_string("<resource/>");
        } else {
            ErrorPages::func(1, $fileMissingMessage);
            exit();
        }
    }

    return $xmlMappingObj;
}

function debug()
{
    global $mt;
    dd([
        "End Time" => (microtime(true) - $mt) * 1000 . " ms",
        "Memory-1" => round(memory_get_usage() / 1048576, 2) . "MB",
        "Memory-2" => round(memory_get_usage(true) / 1048576, 2) . "MB"
    ]);
}

function setPost($name, $val)
{
    $_POST[$name] = $val;
    return $name;
}

function date_show(string $format, int $timestamp = null, string $fallBack = "---")
{
    if ($timestamp === null) {
        $timestamp = getTime();
    }

    return $timestamp ? date($format, $timestamp) : $fallBack;
}

function isAcceptContentType($contentType): bool
{
    return in_array($contentType, array_map("trim", explode(",", getallheaders()['Accept'])));
}