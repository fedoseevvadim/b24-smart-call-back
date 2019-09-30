<?php

use Bitrix\Main\Loader;

$path = "/local/modules/smartcallback";

require($_SERVER["DOCUMENT_ROOT"] . $path . "/lib/API.php");
require($_SERVER["DOCUMENT_ROOT"] . $path . "/lib/struct.php");
require($_SERVER["DOCUMENT_ROOT"] . $path . "/lib/statTable.php");
require($_SERVER["DOCUMENT_ROOT"] . $path . "/lib/cron.php");

$arClasses = array(
    'SmartCallBack\API' => 'lib/API.php',
    'SmartCallBack\Struct' => 'lib/struct.php',
    'SmartCallBack\StatTable' => 'lib/statTable.php',
    'SmartCallBack\Cron' => 'lib/cron.php',
);

Loader::registerAutoLoadClasses(
    "smartcallback", $arClasses
);
