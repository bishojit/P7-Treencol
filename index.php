<?php

include "core/function.php";
include "vendor/autoload.php";

use Core\AppInit;
use Core\Auth;
use Core\DbConnect;
use Core\Method;
use Core\Route;
use Core\SoftInfo;
use Core\SystemDefaults;
use Core\TimeZone;

$mt = microtime(true);

//--App Initialize
$AppInit = new AppInit();

//--Route
$Route = new Route($AppInit);

//--DB Connect
$DbConnect = new DbConnect($AppInit);
$pdo = $DbConnect->connect();

//--SystemDefaults
$SystemDefaults = new SystemDefaults($AppInit);

//--Timezone
$TimeZone = new TimeZone();

//--SoftInfo
$SoftInfo = new SoftInfo($AppInit);

//--Auth Checking
$Auth = new Auth($SystemDefaults, $AppInit->getUserIndex());

//--Include App Functions
include "app/app-functions.php";

//--Method
$method = new Method($Route);

//debug();