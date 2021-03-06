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

    private $_entity = "LEAD";
    private $_connection;
    private $_tblPerm = "b_crm_entity_perms";

    // Options in module, map fields via ID of lead or deal
    public static $arrUtm = [
        "UTM_SOURCE",
        "UTM_MEDIUM",
        "UTM_CAMPAIGN",
        "UTM_TERM",
        "UTM_CONTENT",
        "UTM_UPDATED",
        "DOMEN" //  Да потому что он у них так называется в системе
    ];


    function __construct() {

        foreach ( self::$arrUtm as $utm ) {
            $this->arrStruct[$utm]      = \COption::GetOptionString(Struct::MODULE_ID, $utm);
        }

        $this->_connection = \Bitrix\Main\Application::getConnection();

    }


    public function addObject ( array $array, $class ): int {

        $arrStruct = $this->getStruct();
        $arrStruct["HAS_PHONE"] = "Y";
        $userID = $array["CREATED_BY_ID"];

        $arrStruct["CREATED_BY_ID"]     = $array["CREATED_BY_ID"];
        $arrStruct["MODIFY_BY_ID"]      = $array["MODIFY_BY_ID"];
        $arrStruct["ASSIGNED_BY_ID"]    = $array["ASSIGNED_BY_ID"];

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

            $id = $res->getId();

            // attributes
            $arrAtributes = [
                "D57", // ХЗ
                "IU".$userID,
                "O",
                "STATUS_IDNEW",
                "U".$userID
            ];

            // add permissions

            foreach ( $arrAtributes as $atribute ) {

                $sql = "
                            INSERT INTO $this->_tblPerm (ENTITY,  ENTITY_ID, ATTR) 
                            VALUES ('$this->_entity', '$id', '$atribute')
                        ";

                $this->_connection->query($sql);

            }

            // add phone number to additional table
            $arrMT = [
                "ENTITY_ID"     => $this->_entity,
                "ELEMENT_ID"    => $id,
                "TYPE_ID"       => "PHONE",
                "VALUE_TYPE"    => "WORK",
                "COMPLEX_ID"    => "PHONE_WORK",
                "VALUE"         => $iPhone
            ];

            \Bitrix\Crm\FieldMultiTable::add($arrMT);

            return $id;

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