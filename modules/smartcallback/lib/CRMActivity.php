<?php

namespace SmartCallBack;

class CRMActivity extends \CCrmActivity{

    private $_userId;
    private $_phone;
    private $_dealID;
    private $_callID;

    private $_providerName = "VOXIMPLANT_CALL";
    private $_typeId = 2;
    private $_resultStrem  = 1;
    private $_direction = 1;
    private $_ownerTypeIdLead = 1;  // LEAD
    private $_ownerTypeIdDeal = 2;  // DEAL
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
    * @param $duration - duration it is time of call
    */
    public function addActivity ( array $storageElements, int $duration, string $ownerType) {

        $owner = 1;

        switch ($ownerType) {

            case "deal":

                $owner = $this->_ownerTypeIdDeal;
                break;

            case "lead":

                $owner = $this->_ownerTypeIdLead;
                break;
        }

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
            "OWNER_TYPE_ID"         => $owner,
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

    private function callDurationAsText ( int $duration ) {

        $return = "";

        if ( isset( $duration ) ) {

            $min = (int) ( $duration / 60 );
            $sec = $duration % 60;

            $return = $min . " " . $this->_min . ", " . $sec . " " . $this->sec;
        }

        return $return;

    }

    public function addCallAndActivity ( string $ownerType, array $item ) {

        $userID     = $this->_userId;
        $phone      = $this->_phone;
        $dealID     = $this->_dealID;
        $duration   = (int) $item['duration'];

        if ( $duration > 0 ) {

            // Create a call
            $VIcall = new VICall( $userID, $phone, $dealID );
            $ID     = $VIcall->createCall($duration, $dealID);
            $callId = $VIcall->callID; // Получим ID звонка

            $this->_callID = $callId;

            // Создадим Activity
            $this->addActivity   (
                [$item['id_record_bx']],
                $duration,
                $ownerType
            );

        }

    }

}




