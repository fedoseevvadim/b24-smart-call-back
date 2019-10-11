<?php

namespace SmartCallBack;

use Bitrix\Crm\LeadTable;

use Bitrix\Main\Type\DateTime;

// CLIENT_TOKEN - YoQMjsJcGlHSXP8HqjUo
// API_TOKEN - z3tF2u4ZMnA1J8w76YI5
// API_SIGNATURE - SzFCC2PeLUxOhf1b


class Lead extends CRMObject implements CRMStruct {

    public $title       = "Новый лид по звонку с [PHONE_NUMBER] из SmartCallBack";

    public $mainClassObject = "\Bitrix\Crm\LeadTable";

    /**
    * array of structure to create lead
    *
    */
    public function getStruct (): array {

        $arrStruct = [
            "STATUS_ID"             => "NEW",
            "STATUS_SEMANTIC_ID"    => "P"
        ];

        $arrBaseStruct = Struct::getCrmStruct();

        return array_merge($arrStruct, $arrBaseStruct);

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