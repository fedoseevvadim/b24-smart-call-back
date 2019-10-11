<?php

namespace SmartCallBack;

use Bitrix\Crm\LeadTable;
use Bitrix\Main\Type\DateTime;

class Deal extends CRMObject implements CRMStruct {

    public $title     = 'Новая сделка по звонку с [PHONE_NUMBER] из SmartCallBack';
    public $stage     = "C4:NEW";
    public $category  = 4;

    public $mainClassObject = "\Bitrix\Crm\LeadTable";

    /**
    * array of structure to create deal
    *
    */
    public function getStruct (): array {

        return [
            "DATE_CREATE"       => new DateTime(),
            "DATE_MODIFY"       => new DateTime(),
            "CREATED_BY_ID"     => Struct::userId,
            "MODIFY_BY_ID"      => Struct::userId,
            "ASSIGNED_BY_ID"    => Struct::userId,
            "STAGE_SEMANTIC_ID" => P,
            "IS_NEW"            => $this->isNew,
            "COMPANY_ID"        => 0,
            "OPENED"            => N,
            "CATEGORY_ID"       => $this->category,
            "STAGE_ID"          => $this->stage,
        ];
    }


    /**
     * Add a deal
     *
     * @param array array of fields
     */
    public function add (array $array ): int {

        return $this->addObject( $array, $this->mainClassObject );

    }


}