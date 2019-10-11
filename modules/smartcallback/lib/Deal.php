<?php

namespace SmartCallBack;

use Bitrix\Crm\LeadTable;
use Bitrix\Main\Type\DateTime;

class Deal extends CRMObject implements CRMStruct {

    public $title     = 'Новая сделка по звонку с [PHONE_NUMBER] из SmartCallBack';
    public $stage     = "C4:NEW";
    public $mainClassObject = "\Bitrix\Crm\LeadTable";

    /**
    * array of structure to create deal
    *
    */
    public function getStruct (): array {

        return Struct::getCrmStruct();

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