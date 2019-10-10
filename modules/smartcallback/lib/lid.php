<?php

namespace SmartCallBack;

use Bitrix\Crm\DealTable,
    Bitrix\Crm\LeadTable,
    Bitrix\Crm\EO_Utm;

use Bitrix\Main\Type\DateTime;

// CLIENT_TOKEN - YoQMjsJcGlHSXP8HqjUo
// API_TOKEN - z3tF2u4ZMnA1J8w76YI5
// API_SIGNATURE - SzFCC2PeLUxOhf1b


class LID {

    private $_userId    = 10;
    private $_titleLead = "Новый лид по звонку с из SmartCallBack";
    private $_titleDeal = "Новая сделка по звонку с из SmartCallBack";
    private $_stage     = "C4:NEW";
    private $_category  = 4;
    private $_isNew     = "Y";

    public $arrStruct = [];

    function __construct() {

        $this->arrStruct["utm_source"]      = \COption::GetOptionString(Struct::moduleID, "UTM_SOURCE");
        $this->arrStruct["utm_medium"]      = \COption::GetOptionString(Struct::moduleID, "UTM_MEDIUM");
        $this->arrStruct["utm_campaign"]    = \COption::GetOptionString(Struct::moduleID, "UTM_CAMPAIGN");
        $this->arrStruct["utm_term"]        = \COption::GetOptionString(Struct::moduleID, "UTM_TERM");
        $this->arrStruct["utm_content"]     = \COption::GetOptionString(Struct::moduleID, "UTM_CONTENT");
        $this->arrStruct["utm_updated"]     = \COption::GetOptionString(Struct::moduleID, "UTM_UPDATED");
        $this->arrStruct["PHONE_WORK"]      = "phone";



    }

    /**
    * array of structure to create lid or deal
    *
    */
    private function getStruct (): array {

        return [
                "DATE_CREATE"       => new DateTime(),
                "DATE_MODIFY"       => new DateTime(),
                "CREATED_BY_ID"     => $this->_userId,
                "MODIFY_BY_ID"      => $this->_userId,
                "ASSIGNED_BY_ID"    => $this->_userId,
                "STAGE_SEMANTIC_ID" => P,
                "IS_NEW"            => $this->_isNew,
                "COMPANY_ID"        => 0,
                "OPENED"            => N,
                "CATEGORY_ID"       => $this->_category,
                "STAGE_ID"          => $this->_stage,
            ];
    }


//    private function mapFields ( $array ): array  {
//
//        return array_intersect_key($array, $this->arrStruct);
//
//    }

    /**
    * Add a deal
    *
    * @param array array of fields
    */
    public function addDeal (array $array ): int {

        return $this->addObject($array, "deal");

    }

    /**
     * Add a lead
     *
     * @param array array of fields
     */
    public function addLead ( array $array ): int {

        return $this->addObject($array, "lead");

    }

    private function addObject ( array $array, $typeOfObj ): int {

        $arrStruct  = $this->getStruct();
        $arrStruct["WORK_PHONE"] = $array['phone'];
        $arrStruct["HAS_PHONE"] = "Y";

        //$arrmap     = array_intersect_key($array, $this->arrStruct);
        //$array      = array_merge($arrStruct, $arrmap);

        $array = $arrStruct;

        switch ($typeOfObj) {

            case "deal":

                $array["TITLE"] = $this->_titleDeal;
                $res = \Bitrix\Crm\DealTable::add( $array );

                break;

            case "lead":

                $array["TITLE"] = $this->_titleLead;
                $res = \Bitrix\Crm\LeadTable::add( $array );

                break;
        }

        if ($res->isSuccess()) {

            // add phone numer to additional table
            $arrMT = [
                "ENTITY_ID" => "LEAD",
                "ELEMENT_ID" => $res->getId(),
                "TYPE_ID" => "PHONE",
                "VALUE_TYPE" => "WORK",
                "COMPLEX_ID" => "PHONE_WORK",
                "VALUE" => $arrStruct["WORK_PHONE"]
            ];

            \Bitrix\Crm\FieldMultiTable::add($arrMT);

            return $res->getId();

        }  else  {
            print_r($res->getErrorMessages());
        }

    }

    /**
     * Check existence of LID
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