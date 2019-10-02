<?php

use Bitrix\Main\Loader;

\Bitrix\Main\Loader::registerAutoLoadClasses(
    "smartcallback",
    array(
        'SmartCallBack\API' => 'lib/API.php',
        'SmartCallBack\Struct' => 'lib/struct.php',
        'SmartCallBack\StatTable' => 'lib/statTable.php',
        'SmartCallBack\Cron' => 'lib/cron.php',
        'SmartCallBack\statItems' => 'lib/statItem.php',
        'SmartCallBack\downloadItems' => 'lib/downloadItems.php',
        'SmartCallBack\LID' => 'lib/lid.php',
    )
);