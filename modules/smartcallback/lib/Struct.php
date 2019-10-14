<?php

namespace SmartCallBack;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Diag\Debug;

final class Struct {

    const debug = 1; // Set it to 0 if you do not want to write into a file in root dir __bx_log.log

    const moduleID          = "smartcallback";
    const resForLastDays    = 30;             // get data for last XX days
    const userId            = 1;
    const category          = 4;
    const companyID         = 0;
    const opened            = "Y";
    const isNew             = "Y";
    const stageSemanticID   = "P";
    const stage             = "C4:NEW";
    const dateFormat        = "d.m.Y H:i:s"; // for log files

    public static function getCrmStruct (): array {

        return [
            "DATE_CREATE"       => new DateTime(),
            "DATE_MODIFY"       => new DateTime(),
            "CREATED_BY_ID"     => Struct::userId,
            "MODIFY_BY_ID"      => Struct::userId,
            "ASSIGNED_BY_ID"    => Struct::userId,
            "STAGE_SEMANTIC_ID" => self::stageSemanticID,
            "IS_NEW"            => self::isNew,
            "COMPANY_ID"        => self::companyID,
            "OPENED"            => self::opened,
            "CATEGORY_ID"       => self::category,
            "STAGE_ID"          => self::stage,
        ];

    }


    public function debug( $debug ) {

        if ( self::debug === 1 ) {
            Debug::writeToFile(date(self::dateFormat) .  " " . $debug);
        }

    }
}

