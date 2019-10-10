<?php

namespace SmartCallBack;

use Bitrix\Crm\DealTable,
    Bitrix\Crm\LeadTable;

use Bitrix\Main\Type\DateTime;

class LID {

    private $_userId    = 1;
    private $_title     = "Новый лид по звонку с из SmartCallBack";
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
        $this->arrStruct["phone"];

    }

    /**
    * array of structure to create lid or deal
    *
    */
    private function getStruct (): array {

        return [
                "TITLE"             => $this->_title,
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

        return $this->addObject($array);

    }

    /**
     * Add a lead
     *
     * @param array array of fields
     */
    public function addLead ( array $array ): int {

        return $this->addObject($array);

    }

    private function addObject ( array $array, $typeOfObj ): int {

        $arrStruct  = $this->getStruct();
        $arrmap     = array_intersect_key($array, $this->arrStruct);
        $array      = array_merge($arrStruct, $arrmap);

        switch ($typeOfObj) {

            case "deal":

                $res = \Bitrix\Crm\DealTable::add( $array );
                break;

            case "lead":

                $res = \Bitrix\Crm\LeadTable::add( $array );
                break;
        }

        if ($res->isSuccess()) {
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
    public function checkIfExist ( int $phone ): int {

        $arLeads = \Bitrix\Crm\LeadTable::getList(array(
            "filter" => array("PHONE" => $phone)
        ))->fetchAll();


        foreach ( $arLeads as $lead ) {

        }

    }


}