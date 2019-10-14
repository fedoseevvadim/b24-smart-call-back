<?php

namespace SmartCallBack;

use Bitrix\Main\Application;
use Bitrix\Main\Diag\Debug;

use \Bitrix\Crm\LeadTable,
    \Bitrix\Crm\DealTable,
    \Bitrix\Crm\FieldMultiTable;

class CRMObject  {

    public $arrStruct = [];
    public $mainClassObject;

    // Options in module, map fields via ID of lead or deal
    public static $arrUtm = [
        "UTM_SOURCE",
        "UTM_MEDIUM",
        "UTM_CAMPAIGN",
        "UTM_TERM",
        "UTM_CONTENT",
        "UTM_UPDATED"
    ];


    function __construct() {

        foreach ( self::$arrUtm as $utm ) {
            $this->arrStruct[$utm]      = \COption::GetOptionString(Struct::moduleID, $utm);
        }

    }


    public function addObject ( array $array, $class ): int {

        $arrStruct  = $this->getStruct();
        $arrStruct["HAS_PHONE"] = "Y";
        $iPhone = $array['phone'];

        // add utm marks
        foreach ( $this->arrStruct as $keyS => $struct ) {

            if ( $this->arrStruct[$keyS] ) {

                $keyS = strtolower($keyS);

                if ( array_key_exists($keyS, $array) ) {

                    $arrStruct[$struct] = $array[$keyS];
                }

            }

        }


        $array = $arrStruct;

        //$array = array_merge($array, $arrStruct);

        $array["TITLE"] = str_replace("[PHONE_NUMBER]", $iPhone, $this->title); // replace [PHONE_NUMBER] title with real phone;
        $res = $class::add( $array );

        if ($res->isSuccess()) {

            // add phone number to additional table
            $arrMT = [
                "ENTITY_ID"     => "LEAD",
                "ELEMENT_ID"    => $res->getId(),
                "TYPE_ID"       => "PHONE",
                "VALUE_TYPE"    => "WORK",
                "COMPLEX_ID"    => "PHONE_WORK",
                "VALUE"         => $iPhone
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

        $arLeads = $this->mainClassObject::getList([
            "filter" => array("PHONE" => $phone)
        ])->fetchAll();

        return $arLeads;

    }

}