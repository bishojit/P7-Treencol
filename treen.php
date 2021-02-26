<?php

//--Constants
define("__MOTHER_SERVER__", "https://raw.treencol.com/v6");
define("__MOTHER_LINK__", __MOTHER_SERVER__ . "/.treen.php");
define("__DELAY_MS__", 100);

include "core/function.php";
include "vendor/autoload.php";

use Packages\treen\TreenProcess;

//--VARs
$TreenProcess = new TreenProcess();
$TreenProcess->process(is_array($argv) ? $argv : []);
