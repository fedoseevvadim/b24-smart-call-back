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

    if ( CModule::IncludeModule("voximplant")) {

        SmartCallBack\Cron::createObj();
        SmartCallBack\Cron::writeCallsToB24();

//        $userID = 1;
//        $phone = "79152955011";
//        $dealID = 41;
//        $duration = 144;
//        // Создадим звонок
//        $VIcall = new VICall( $userID, $phone, $dealID );
//        $ID     = $VIcall->createCall($duration, $dealID);
//        $callId = $VIcall->callID; // Получим ID звонка
//
//        // Создадим Activity
//        $crmActivity = new \SmartCallBack\crmActivity( $userID, $phone, $dealID, $callId );
//        $crmActivity->addActivity([103], $duration);


    }

}