<?php

namespace SmartCallBack;

use Bitrix\Crm\LeadTable;
use Bitrix\Crm\DealTable;
//use SmartCallBack\CRMStruct;

class CRMObject  {

    public $arrStruct = [];
    public $opened    = "Y";
    public $isNew     = "Y";


    function __construct() {

        $this->arrStruct["utm_source"]      = \COption::GetOptionString(Struct::moduleID, "UTM_SOURCE");
        $this->arrStruct["utm_medium"]      = \COption::GetOptionString(Struct::moduleID, "UTM_MEDIUM");
        $this->arrStruct["utm_campaign"]    = \COption::GetOptionString(Struct::moduleID, "UTM_CAMPAIGN");
        $this->arrStruct["utm_term"]        = \COption::GetOptionString(Struct::moduleID, "UTM_TERM");
        $this->arrStruct["utm_content"]     = \COption::GetOptionString(Struct::moduleID, "UTM_CONTENT");
        $this->arrStruct["utm_updated"]     = \COption::GetOptionString(Struct::moduleID, "UTM_UPDATED");
        $this->arrStruct["PHONE_WORK"]      = "phone";


    }


    public function addObject ( array $array, $class ): int {

        $arrStruct  = $this->getStruct();
        //$arrStruct["WORK_PHONE"] = $array['phone'];
        $arrStruct["HAS_PHONE"] = "Y";

        //$arrmap     = array_intersect_key($array, $this->arrStruct);
        //$array      = array_merge($arrStruct, $arrmap);

        $array = $arrStruct;

        $array["TITLE"] = str_replace("[PHONE_NUMBER]", $array['phone'], $this->_title); // replace [PHONE_NUMBER] title with real phone;
        $res = $class::add( $array );

        if ($res->isSuccess()) {

            // add phone number to additional table
            $arrMT = [
                "ENTITY_ID"     => "LEAD",
                "ELEMENT_ID"    => $res->getId(),
                "TYPE_ID"       => "PHONE",
                "VALUE_TYPE"    => "WORK",
                "COMPLEX_ID"    => "PHONE_WORK",
                "VALUE"         => $array['phone']
            ];

            \Bitrix\Crm\FieldMultiTable::add($arrMT);

            return $res->getId();

        }  else  {
            print_r($res->getErrorMessages());
        }

    }

    /**
    * Check existence of object
    *
    * @param phone
    */
    public function checkIfExist ( int $phone ): array {

        $arLeads = \Bitrix\Crm\LeadTable::getList([
            "filter" => array("PHONE" => $phone)
        ])->fetchAll();

        return $arLeads;

    }

}