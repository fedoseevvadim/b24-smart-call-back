<?php

namespace SmartCallBack;

use Bitrix\Main\Type\DateTime;

final class Struct {

    const moduleID          = "smartcallback";
    const resForLastDays    = 30;             // get data for last XX days
    const userId            = 1;
    const category          = 4;
    const companyID         = 0;
    const opened            = "Y";
    const isNew             = "Y";
    const stageSemanticID   = "P";
    const stage             = "C4:NEW";

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

}

