<?php

namespace SmartCallBack;

use Protobuf\Exception;


class Cron  {

    public $date_to;
    public $date_from;

    const dateFormat = "d.m.Y H:i:s"; // for log files
    const pathToLog = "/local/modules/smartcallback/error.log"; //

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

        //echo $_SERVER["DOCUMENT_ROOT"];

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
    public static function createLids () {

        $statItems  = new statItems();
        $lid        = new LID();
        $arrElements = $statItems->getItemsWithOutLid();

        foreach ( $arrElements as $item ) {

            // TODO
            // Нужно дописать код который будет проверять если такой лид по номеру телефона

            $lidID = $lid->addDeal($item);

            if ( $lidID > 0 ) {

                $arrUpdate = [
                    'lid' => $lidID,
                ];

                $statItems->updateItem($item['id'], $arrUpdate);

                $userID     = 1;
                $phone      = $item['phone'];
                $dealID     = $lidID;
                $duration   = $item['duration'];

                // Создадим звонок
                $VIcall = new VICall( $userID, $phone, $dealID );
                $ID     = $VIcall->createCall($duration, $dealID);
                $callId = $VIcall->callID; // Получим ID звонка

                // Создадим Activity
                $crmActivity = new \SmartCallBack\crmActivity( $userID, $phone, $dealID, $callId );
                $crmActivity->addActivity([$item['id_record_bx']], $duration);

            }


        }

    }


    /**
    * write call to B24
    */
    public function writeCallsToB24() {

        $statItems  = new statItems();
        $arrElements = $statItems->getWroteCalls();

        foreach ( $arrElements as $elem ) {

            if ( strlen($elem["record_url"]) > 0 ) {

                $file_name = basename($elem["record_url"]); // get file name from url
                $pathToFile = $_SERVER['DOCUMENT_ROOT'] . \SmartCallBack\downloadItems::downloadDir . $file_name;

                $storage = \Bitrix\Disk\Driver::getInstance()->getStorageByUserId(1);
                $folder = $storage->getRootObject();

                if ($folder) {

                    if ($storage) {

                        $fileArray  = \CFile::MakeFileArray($pathToFile);
                        $arrData    = ['CREATED_BY' => 1];

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