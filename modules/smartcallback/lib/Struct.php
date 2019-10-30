<?php

namespace SmartCallBack;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Diag\Debug;

final class Struct {

    const DEBUG = 1; // Set it to 0 if you do not want to write into a file in root dir __bx_log.log

    const MODULE_ID             = "smartcallback";
    const RES_FOR_LAST_DAYS    = 10;             // get data for last XX days
    const USER_ID               = 1;
    const CATEGORY              = 4;
    const COMPANY_ID            = 0;
    const OPENED                = "Y";
    const IS_NEW                = "Y";
    const STAGE_SEMANTIC_ID     = "P";
    const STAGE                 = "C4:NEW";
    const DATE_FORMAT           = "d.m.Y H:i:s"; // for log files

    public static function getCrmStruct (): array {

        return [
            "DATE_CREATE"       => new DateTime(),
            "DATE_MODIFY"       => new DateTime(),
            "CREATED_BY_ID"     => Struct::USER_ID,
            "MODIFY_BY_ID"      => Struct::USER_ID,
            "ASSIGNED_BY_ID"    => Struct::USER_ID,
            "STAGE_SEMANTIC_ID" => self::STAGE_SEMANTIC_ID,
            "IS_NEW"            => self::IS_NEW,
            "COMPANY_ID"        => self::COMPANY_ID,
            "OPENED"            => self::OPENED,
            "CATEGORY_ID"       => self::CATEGORY,
            "STAGE_ID"          => self::STAGE,
        ];

    }


    public function debug( $debug ) {

        if ( self::DEBUG === 1 ) {
            Debug::writeToFile(date(self::DATE_FORMAT) .  " " . $debug);
        }

    }
}

