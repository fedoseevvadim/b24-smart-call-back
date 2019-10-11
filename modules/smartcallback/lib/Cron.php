<?php

namespace SmartCallBack;

use \Bitrix\Disk\Driver;

class Cron  {

    public $date_to;
    public $date_from;

    const dateFormat = "d.m.Y H:i:s"; // for log files
    const pathToLogStatus = "/local/modules/smartcallback/status.log"; //
    const pathToLog = "/local/modules/smartcallback/error.log"; //

    public $userId = 1; // by default

    function __construct() {

//        $this->date_to    = time();
//        $this->date_from  = $this->date_to - ( Struct::resForLastDays * 24 * 60 * 60 ) ;

    }

//    private function getOption($name) {
//        return \COption::GetOptionString(Struct::moduleID, $name);
//    }

    /**
     * Task for cron write items into database
     *
     */
    public static function  writeItems() {

        $statItems = new StatItems();

        $date_to     = time();
        $date_from  = $date_to - ( Struct::resForLastDays * 24 * 60 * 60 ) ;

        $result = file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLogStatus, date(self::dateFormat) . " Start");

        $POST = Array(
            "date_from" => $date_from,
            "date_to"   => $date_to,
        );


        // get API, TOKEN, SIGNATURE
        //$arrModuleSettings = ['CLIENT_TOKEN', 'API_TOKEN', 'API_SIGNATURE'];
        //list ( $CLIENT_TOKEN, $API_TOKEN, $API_SIGNATURE ) = self::getOption($arrModuleSettings);

        //list($CLIENT_TOKEN, $API_TOKEN, $API_SIGNATURE) =
        $CLIENT_TOKEN   = \COption::GetOptionString(Struct::moduleID, "CLIENT_TOKEN");
        $API_TOKEN      = \COption::GetOptionString(Struct::moduleID, "API_TOKEN");
        $API_SIGNATURE  = \COption::GetOptionString(Struct::moduleID, "API_SIGNATURE");

        if ( $CLIENT_TOKEN AND $API_TOKEN AND $API_SIGNATURE ) {

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
                file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLogStatus, $e->getMessage());
            }

        } else {
            file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLogStatus, "Empty Autorisation data, please check CLIENT_TOKEN, API_TOKEN, API_SIGNATURE");
        }

        return "\SmartCallBack\Cron::writeItems();";

    }

    /**
     * Downloading mp3 items
     *
     */
    public static function downloadItems() {

        $download   = new DownloadItems();
        $statItems  = new StatItems();
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

        return "\SmartCallBack\Cron::DownloadItems();";
    }


    /**
     * creating lids in CRM system B24
     *
     */
    public static function createObj () {

        $statItems  = new StatItems();
        $lead        = new Lead;
        $deal        = new Deal;

        $bCreateLead  = \COption::GetOptionString(Struct::moduleID, "CREATE_LEAD");
        $bCreateDeal  = \COption::GetOptionString(Struct::moduleID, "CREATE_DEAL");

        if ( $bCreateLead === "Y" ) {
            $arrElements = $statItems->getItemsWithOutLead();
            $ownerType = "lead";
        }

        if ( $bCreateDeal === "Y" ) {
            $arrElements = $statItems->getItemsWithOutDeal();
            $ownerType = "deal";
        }

        $userID = (int) \COption::GetOptionString(Struct::moduleID, "MAIN_USER_OPTION");

        // If in module settings checked one of options
        if ( $bCreateLead === "Y" OR $bCreateDeal === "Y" ) {

            foreach ( $arrElements as $item ) {

                $arrElem = $lead->checkIfExist($item['phone']); // Search element lead or deal with that phone number

                if ( count($arrElem) > 0 ) {

                    foreach ( $arrElem as $lead ) {

                        $arrUpdate = [
                            'lead' => $lead['ID'],
                        ];

                        $statItems->updateItem( $item['id'], $arrUpdate );

                        $crmActivity = new CRMActivity($userID, $item['phone'], $lead['ID'], $ownerType);
                        $crmActivity->addCallAndActivity($ownerType, $item);

                    }

                } else {

                    if ( $bCreateLead === "Y" ) {

                        $ID = $lead->add($item);

                        $arrUpdate = [
                            'lead' => $ID,
                        ];

                    }

                    if ( $bCreateDeal === "Y" ) {

                        $ID = $deal->add($item);

                        $arrUpdate = [
                            'deal' => $ID,
                        ];

                    }
                }


                if ( $ID > 0 ) {

                    try {

                        $statItems->updateItem($item['id'], $arrUpdate);
                        $crmActivity = new CRMActivity($userID, $item['phone'], $ID, $ownerType);
                        $crmActivity->addCallAndActivity($ownerType, $item);

                    } catch (Exception $e) {
                        file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLogStatus, " Something went wrong while creating a Call");
                    }

                }
            }
        }

        return "\SmartCallBack\Cron::createObj();";
    }


//    public function writeCallsToB24_2() {
//
//        file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, "test2");
//
//        return "\SmartCallBack\Cron::writeCallsToB24_2();";
//
//    }

    /**
    * write call to B24
    */
    public function writeCallsToB24() {

        $statItems      = new StatItems();
        $arrElements    = $statItems->getWroteCalls();
        $userID         = (int) \COption::GetOptionString(Struct::moduleID, "MAIN_USER_OPTION");

        file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, "test6");

        foreach ( $arrElements as $elem ) {

            if ( !$elem["record_url"] ) {
                continue;
            }

            $file_name = basename($elem["record_url"]); // get file name from url
            $pathToFile = $_SERVER['DOCUMENT_ROOT'] . \SmartCallBack\DownloadItems::downloadDir . $file_name;

            try {

                $storage = \Driver::getInstance()->getStorageByUserId($userID);
                $folder = $storage->getRootObject();

                if ( !$folder OR !$storage ) {

                    throw new Exception("Folder or storage folder does not exist");
                }

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

            } catch ( Exception $e ) {
                echo $e;
                file_put_contents($_SERVER["DOCUMENT_ROOT"].self::pathToLog, $e);

            }
        }

        return "\SmartCallBack\Cron::writeCallsToB24();";
    }
}