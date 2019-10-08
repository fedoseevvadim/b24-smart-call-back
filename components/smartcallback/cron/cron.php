#!/usr/bin/php
<?php

set_time_limit(6000);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CRONTAB", true);
define('BX_WITH_ON_AFTER_EPILOG', true);
define('BX_NO_ACCELERATOR_RESET', true);

$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www/";

require( $_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php" );

use SmartCallBack\API,
    SmartCallBack\Struct,
    SmartCallBack\Cron;
use SmartCallBack\downloadItems;
use SmartCallBack\VICall;

//use Bitrix\Voximplant\Call as VI;

if ( CModule::IncludeModule("smartcallback")) {

    if ( CModule::IncludeModule("voximplant")) {

        $userID = 1;
        $phone = "79152955011";
        $dealID = 41;
        $duration = 144;
        // Создадим звонок
        $VIcall = new VICall( $userID, $phone, $dealID );
        $ID     = $VIcall->createCall($duration, $dealID);
        $callId = $VIcall->callID; // Получим ID звонка

        // Создадим Activity
        $crmActivity = new \SmartCallBack\crmActivity( $userID, $phone, $dealID, $callId );
        $crmActivity->addActivity([103], $duration);


    }
}