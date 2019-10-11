#!/usr/bin/php
<?php

set_time_limit(6000);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CRONTAB", true);
define('BX_WITH_ON_AFTER_EPILOG', true);
define('BX_NO_ACCELERATOR_RESET', true);

echo $_SERVER["DOCUMENT_ROOT"];

require( $_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php" );

use SmartCallBack\API,
    SmartCallBack\Struct,
    SmartCallBack\Cron;
use SmartCallBack\DownloadItems;


if ( CModule::IncludeModule("smartcallback")) {

    \SmartCallBack\Cron::createObj();
    \SmartCallBack\Cron::writeCallsToB24();

}