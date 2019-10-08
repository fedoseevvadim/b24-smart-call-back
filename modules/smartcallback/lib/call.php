<?php

namespace SmartCallBack;

use \Bitrix\Voximplant\Call;


class VICall extends Call {

    private $_restAppName       = "SmartCallBack";
    private $_rest_API_name     = "Smart Call Back";
    private $_entityType        = "LEAD";
    private $_prefix            = "externalCall.";
    private $_callCategory      = "external";
    private $_incoming          = 2;
    private $_restAppID         = 2;
    private $_callStatus        = 1;
    private $_callStorageType   = 3;
    private $_callCode          = 200;

    private $_userId;
    private $_phone;
    private $_dealID;

    public $callID;

    function __construct( int $userID, int $phone, int $dealID ) {

        $this->_userId = $userID;
        $this->_phone = $phone;
        $this->_dealID = $dealID;
    }


    function createCall ( int $callDuration, int $callRecordId ): int {

        $callId = $this->_prefix . md5(uniqid($this->_rest_API_name . $this->_userId . $this->_phone)) . '.' . time();
        $this->callID = $callId;

        $arrayVI = [
            "PORTAL_USER_ID"        => $this->_userId,
            "PHONE_NUMBER"          => $this->_phone,
            "INCOMING"              => $this->_incoming,
            "PORTAL_NUMBER"         => "REST_APP:2",
            "CALL_FAILED_CODE"      => $this->_callCode,
//            "CALL_WEBDAV_ID" => 103,
//            "CRM_ACTIVITY_ID" => 574,
            "REST_APP_ID"           => $this->_restAppID,
            "CALL_ID"               => $callId,
            "CALL_CATEGORY"         => $this->_callCategory,
            "CALL_DURATION"         => $callDuration,
            "CALL_START_DATE"       => new \Bitrix\Main\Type\DateTime(),
            "CALL_RECORD_ID"        => $callRecordId,
            "CRM_ENTITY_TYPE"       => $this->_entityType,
            "CRM_ENTITY_ID"         => $this->_dealID,
            "REST_APP_NAME"         => $this->_rest_API_name,
            "CALL_STATUS"           => $this->_callStatus,
            "STORAGE_TYPE_ID"       => $this->_callStorageType,
        ];

        try {

            $resVoxImplant = \Bitrix\Voximplant\StatisticTable::add(
                $arrayVI
            );

        } catch ( Exception $e ) {

            echo 'Caught exeption: ' . $e->getMessage();

        }


        try {

            $result1 = \Bitrix\Voximplant\Model\CallCrmEntityTable::add(
                [
                    "CALL_ID" => $this->callID,
                    "ENTITY_TYPE" => "LEAD",
                    "ENTITY_ID" => $this->_dealID,
                    "IS_PRIMARY" => "Y",
                    "IS_CREATED" => "Y"
                ]
            );

        } catch ( Exception $e ) {
            echo 'Caught exeption: ' . $e->getMessage();
        }


        return $resVoxImplant->getId();

    }


}

