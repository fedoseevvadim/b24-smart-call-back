<?php

namespace SmartCallBack;

use Bitrix\Crm\LeadTable;

use Bitrix\Main\Type\DateTime;

// CLIENT_TOKEN - YoQMjsJcGlHSXP8HqjUo
// API_TOKEN - z3tF2u4ZMnA1J8w76YI5
// API_SIGNATURE - SzFCC2PeLUxOhf1b


class Lead extends CRMObject implements CRMStruct {

    public $title       = "Новый лид по звонку с [PHONE_NUMBER] из SmartCallBack";
    public $stage       = "C4:NEW";
    public $category    = 4;

    public $mainClassObject = "\Bitrix\Crm\LeadTable";

    /**
    * array of structure to create lead
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
                "OPENED"            => $this->opened,
                "CATEGORY_ID"       => $this->category,
                "STAGE_ID"          => $this->stage,

                "STATUS_ID"             => "NEW",
                "STATUS_SEMANTIC_ID"    => "P"
            ];

    }


    /**
     * Add a lead
     *
     * @param array array of fields
     */
    public function add (array $array ): int {

        return $this->addObject ( $array, $this->mainClassObject );

    }

}