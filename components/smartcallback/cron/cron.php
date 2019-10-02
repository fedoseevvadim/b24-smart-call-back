#!/usr/bin/php
<?php

set_time_limit(6000);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CRONTAB", true);
define('BX_WITH_ON_AFTER_EPILOG', true);
define('BX_NO_ACCELERATOR_RESET', true);

require( $_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php" );

use SmartCallBack\API,
    SmartCallBack\Struct,
    SmartCallBack\Cron;
use SmartCallBack\downloadItems;


if ( CModule::IncludeModule("smartcallback")) {

    $cron = new SmartCallBack\Cron();
//    $cron->writeItems();
//    $cron->downloadItems();
    //$cron->createLids();

    $LID = new SmartCallBack\LID;

    $LID->addDeal();

}