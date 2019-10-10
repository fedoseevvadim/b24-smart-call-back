<?php

namespace SmartCallBack;

use Protobuf\Exception;
use \Bitrix\Disk\Driver;

class Cron  {

    public $date_to;
    public $date_from;

    const dateFormat = "d.m.Y H:i:s"; // for log files
    const pathToLog = "/local/modules/smartcallback/error.log"; //

    public $userId = 1; // by default

    function __construct() {

//        $this->date_to    = time();
//        $this->date_from  = $this->date_to - ( Struct::resForLastDays * 24 * 60 * 60 ) ;

    }

    /**
     * Task for cron write items into database
     *
     */
    public static function  writeItems() {

        $statItems = new statItems();

        $date_to     = time();
        $date_from  = $date_to - ( Struct::resForLastDays * 24 * 60 * 60 ) ;

        $result = file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, date(self::dateFormat) . " Start");

        $POST = Array(
            "date_from" => $date_from,
            "date_to"   => $date_to,
        );

        // get API, TOKEN, SIGNATURE
        $CLIENT_TOKEN   = \COption::GetOptionString(Struct::moduleID, "CLIENT_TOKEN");
        $API_TOKEN      = \COption::GetOptionString(Struct::moduleID, "API_TOKEN");
        $API_SIGNATURE  = \COption::GetOptionString(Struct::moduleID, "API_SIGNATURE");

        if ( strlen( $CLIENT_TOKEN ) > 0 AND strlen ( $API_TOKEN ) > 0 AND strlen( $API_SIGNATURE ) > 0 ) {

            try {

                $SmartCallBackAPI = new API(
                    $CLIENT_TOKEN,
                    $API_TOKEN,
                    $API_SIGNATURE
                );

                $SmartCallBackAPI->getQueryList($POST); // make a query to SCB - Smart Call Back

                $result = json_decode($SmartCallBackAPI->RESULT); // decode string

                foreach ( $result->response->queries as $item ) {

                    $statItems->writeItem($item);

                }

            } catch ( Exception $e ) {

                echo 'Caught exeption: ' . $e->getMessage();
                file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, $e->getMessage());
            }

        } else {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, "Empty Autorisation data, please check CLIENT_TOKEN, API_TOKEN, API_SIGNATURE");
        }

        return "\SmartCallBack\Cron::writeItems();";

    }

    /**
     * Downloading mp3 items
     *
     */
    public static function downloadItems() {

        $download   = new downloadItems();
        $statItems  = new statItems();
        $arrItems   = $statItems->getItemsToDownload();

        $arrUpdate = [
            'record_written' => 1,
        ];

        foreach ( $arrItems as $item ) {

            if ( strlen($item['record_url']) === 0 ) { // if url is empty then update item and update field record_written to 1
                $statItems->updateItem($item['id'], $arrUpdate);
            } else {
                $status = $download->download( $item['record_url'] );

                if ( $status > 0 ) {
                    $statItems->updateItem($item['id'], $arrUpdate);
                }
            }
        }

        return "\SmartCallBack\Cron::downloadItems();";
    }


    /**
     * creating lids in CRM system B24
     *
     */
    public static function createObj () {

        $statItems  = new statItems();
        $lid        = new LID();

        $bCreateLead  = \COption::GetOptionString(Struct::moduleID, "CREATE_LEAD");
        $bCreateDeal  = \COption::GetOptionString(Struct::moduleID, "CREATE_DEAL");

        if ( $bCreateLead === "Y" ) {
            $arrElements = $statItems->getItemsWithOutLead();
        }

        if ( $bCreateDeal === "Y" ) {
            $arrElements = $statItems->getItemsWithOutDeal();
        }

        $userID = (int) \COption::GetOptionString(Struct::moduleID, "MAIN_USER_OPTION");

        // If in module settings checked one of options
        if ( $bCreateLead === "Y" OR $bCreateDeal === "Y" ) {

            foreach ( $arrElements as $item ) {

                $ID = $lid->checkIfExist($item['phone']);

                if ( $bCreateLead === "Y" ) {

                    $ID = $lid->addLead($item);
                    $ownerType = "lead";

                    $arrUpdate = [
                        'lead' => $ID,
                    ];

                }

                if ( $bCreateDeal === "Y" ) {

                    $ID = $lid->addDeal($item);
                    $ownerType = "deal";

                    $arrUpdate = [
                        'deal' => $ID,
                    ];

                }

                if ( $ID > 0 ) {

                    try {

                        $statItems->updateItem($item['id'], $arrUpdate);

                        $userID     = $userID;
                        $phone      = $item['phone'];
                        $dealID     = $ID;
                        $duration   = $item['duration'];

                        // Create a call
                        $VIcall = new VICall( $userID, $phone, $dealID );
                        $ID     = $VIcall->createCall($duration, $dealID);
                        $callId = $VIcall->callID; // Получим ID звонка

                        // Создадим Activity
                        $crmActivity = new \SmartCallBack\crmActivity( $userID, $phone, $dealID, $callId );
                        $crmActivity->addActivity   (
                                                    [$item['id_record_bx']],
                                                    $duration,
                                                    $ownerType
                                                    );

                    } catch (Exception $e) {
                        file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, " Something went wrong while creating a Call");
                    }

                }
            }
        }
    }


    /**
    * write call to B24
    */
    public function writeCallsToB24() {

        $statItems      = new statItems();
        $arrElements    = $statItems->getWroteCalls();
        $userID         = (int) \COption::GetOptionString(Struct::moduleID, "MAIN_USER_OPTION");

        foreach ( $arrElements as $elem ) {

            if ( strlen($elem["record_url"]) > 0 ) {

                $file_name = basename($elem["record_url"]); // get file name from url
                $pathToFile = $_SERVER['DOCUMENT_ROOT'] . \SmartCallBack\downloadItems::downloadDir . $file_name;

                $storage = Driver::getInstance()->getStorageByUserId($userID);
                $folder = $storage->getRootObject();

                if ( $folder ) {

                    if ( $storage ) {

                        $fileArray  = \CFile::MakeFileArray($pathToFile);
                        $arrData    = ['CREATED_BY' => $userID];

                        $file = $folder->uploadFile($fileArray, $arrData);

                        $fileID = $file->getId();


                        if ( $fileID > 0 ) {

                            $arrUpdate = [
                                'id_record_bx' => $fileID,
                            ];

                            $statItems->updateItem( $elem['id'], $arrUpdate );
                        }
                    }
                }
            }
        }
    }
}