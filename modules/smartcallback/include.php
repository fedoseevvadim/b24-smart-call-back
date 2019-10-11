<?php

use Bitrix\Main\Loader;

\Bitrix\Main\Loader::registerAutoLoadClasses(
    "smartcallback",
    array(
        'SmartCallBack\API' => 'lib/API.php',
        'SmartCallBack\Struct' => 'lib/Struct.php',
        'SmartCallBack\StatTable' => 'lib/StatTable.php',
        'SmartCallBack\Cron' => 'lib/Cron.php',
        'SmartCallBack\StatItems' => 'lib/StatItems.php',
        'SmartCallBack\DownloadItems' => 'lib/DownloadItems.php',
        'SmartCallBack\Lead' => 'lib/Lead.php',
        'SmartCallBack\Deal' => 'lib/Deal.php',
        'SmartCallBack\VICall' => 'lib/VICall.php',
        'SmartCallBack\CrmActivity' => 'lib/CrmActivity.php',
        'SmartCallBack\CRMStruct' => 'lib/CRMStruct.php',
        'SmartCallBack\CRMObject' => 'lib/CRMObject.php',
    )
);