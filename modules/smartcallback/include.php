<?php

use Bitrix\Main\Loader;

$path = "/local/modules/smartcallback";

require($_SERVER["DOCUMENT_ROOT"] . $path . "/lib/API.php");

$arClasses = array(
    'SmartCallBack\API' => 'lib/API.php',
);

Loader::registerAutoLoadClasses(
    "smartcallback", $arClasses
);
