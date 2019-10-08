<?php

namespace SmartCallBack;


class crmActivity extends \CCrmActivity{

    private $_userId;
    private $_phone;
    private $_dealID;
    private $_callID;

    private $_providerName = "VOXIMPLANT_CALL";
    private $_typeId = 2;
    private $_resultStrem  = 1;
    private $_direction = 1;
    private $_ownerTypeId = 2;  // VoxImplant ID
    private $_priority = 2;
    private $_completed = "Y";
    private $_providerTypeId = "CALL";

    private $_prefix = "VI_";

    private $_subjectPrefix     = "Входящий звонок от ";
    private $_prefixDescription = "Длительность звонка: ";
    private $_min = "мин";
    private $sec  = "сек.";

    function __construct(int $userID, int $phone, int $dealID, $callID) {

        $this->_userId  = $userID;
        $this->_phone   = $phone;
        $this->_dealID  = $dealID;
        $this->_callID  = $callID;

    }

    /*
    * @param $storageElements array of files (their IDs)
    */
    public function addActivity ( array $storageElements, int $duration ) {

        $array = [
            "TYPE_ID"               => $this->_typeId,
            "PROVIDER_ID"           => $this->_providerName,
            "PROVIDER_TYPE_ID"      => $this->_providerTypeId,
            "BINDINGS"              => $this->_dealID,
            "SUBJECT"               => $this->_subjectPrefix . $this->_phone,
            "COMPLETED"             => $this->_completed,
            "RESPONSIBLE_ID"        => $this->_userId,
            "PRIORITY"              => $this->_priority,
            "OWNER_ID"              => $this->_dealID,
            "OWNER_TYPE_ID"         => $this->_ownerTypeId,
            "DESCRIPTION"           => $this->_prefixDescription . $this->callDurationAsText($duration),
            "ORIGIN_ID"             => $this->_prefix.$this->_callID,
            "AUTHOR_ID"             => $this->_userId,
            "DIRECTION"             => $this->_direction,
            "DEADLINE"              => new \Bitrix\Main\Type\DateTime(),
            "RESULT_STREAM"         => $this->_resultStrem,
            "START_TIME"            => new \Bitrix\Main\Type\DateTime(),
            "END_TIME"              => new \Bitrix\Main\Type\DateTime(),
            "STORAGE_ELEMENT_IDS"   => $storageElements,// disk objects

        ];

        try {

            $result = $this->add($array);

        } catch ( Exception $e ) {

            echo 'Caught exeption: ' . $e->getMessage();

        }

    }

    private function callDurationAsText ( $duration ) {

        $return = "";

        if ( isset( $duration ) ) {

            $min = (int) ( $duration / 60 );
            $sec = $duration % 60;

            $return = $min . " " . $this->_min . ", " . $sec . " " . $this->sec;
        }

        return $return;

    }

}




