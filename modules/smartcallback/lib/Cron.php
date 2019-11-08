<?php

namespace SmartCallBack;

use \Bitrix\Disk\Driver;
use Bitrix\Main\Diag\Debug;

class Cron  {

    public $date_to;
    public $date_from;

    const dateFormat = "d.m.Y H:i:s"; // for log files
    //const pathToLogStatus = "/local/modules/smartcallback/status.log"; //
    //const pathToLog = "/local/modules/smartcallback/error.log"; //

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

        $statItems = new StatItems();

        $date_to     = time();
        $date_from   = $date_to - ( Struct::RES_FOR_LAST_DAYS * 24 * 60 * 60 ) ;

        Struct::debug("Start");

        $POST = Array(
            "date_from" => $date_from,
            "date_to"   => $date_to,
        );

        $CLIENT_TOKEN   = \COption::GetOptionString(Struct::MODULE_ID, "CLIENT_TOKEN");
        $API_TOKEN      = \COption::GetOptionString(Struct::MODULE_ID, "API_TOKEN");
        $API_SIGNATURE  = \COption::GetOptionString(Struct::MODULE_ID, "API_SIGNATURE");

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
                Struct::debug($e->getMessage());
            }

        } else {
            Struct::debug( "Empty Autorisation data, please check CLIENT_TOKEN, API_TOKEN, API_SIGNATURE" );
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

        if ( !\CModule::IncludeModule("crm")) {
            Struct::debug("Module crm is not installed");
            return "\SmartCallBack\Cron::createObj();";
        }

        $statItems  = new StatItems();
        $crmLead    = new Lead;
        $crmDeal    = new Deal;

        $bCreateLead  = \COption::GetOptionString(Struct::MODULE_ID, "CREATE_LEAD");
        $bCreateDeal  = \COption::GetOptionString(Struct::MODULE_ID, "CREATE_DEAL");

        if ( $bCreateLead === "Y" ) {
            $arrElements = $statItems->getItemsWithOutLead();
            $ownerType = "lead";
        }

        if ( $bCreateDeal === "Y" ) {
            $arrElements = $statItems->getItemsWithOutDeal();
            $ownerType = "deal";
        }

        $userID = (int) \COption::GetOptionString(Struct::MODULE_ID, "MAIN_USER_OPTION");

        if ( $userID === 0 ) {
            $userID = Struct::USER_ID;
        }

        // If in module settings checked one of options
        if ( $bCreateLead === "Y" OR $bCreateDeal === "Y" ) {

            foreach ( $arrElements as $item ) {

                $arrElem = $crmLead->checkIfExist($item['phone']); // Search element lead or deal with that phone number

                if ( count($arrElem) > 0 ) {

                    foreach ( $arrElem as $lead ) {

                        $arrUpdate = [
                            'lead' => $lead['ID'],
                        ];

                        $bxId = (int) $item['id_record_bx'];

                        $bUpdateItem = false;

                        if ( $bxId > 0 ) {
                            $bUpdateItem = true;
                        }

                        if ( $item["record_url"] === null OR $item["record_url"] === "" ) {
                            $bUpdateItem = true;
                        }

                        if ( $bUpdateItem === true ) {

                            $statItems->updateItem( $item['id'], $arrUpdate );

                            $crmActivity = new CRMActivity($userID, $item['phone'], $lead['ID'], $bxId);
                            $crmActivity->addCallAndActivity($ownerType, $item);
                        }


                    }

                } else {

                    $bCreateItem = false;

                    // if it is empty creating lead
                    if ( $item["record_url"] === null OR $item["record_url"] === "" ) {
                        $bCreateItem = true;
                    } else { // else wait untill mp3 message will be write down on disk

                        if ( $item["id_record_bx"] > 0 ) {
                            $bCreateItem = true;
                        }

                    }

                    $item["CREATED_BY_ID"]     = $userID;
                    $item["MODIFY_BY_ID"]      = $userID;
                    $item["ASSIGNED_BY_ID"]    = $userID;

                    if ( $bCreateLead === "Y" AND $bCreateItem === true ) {

                        $ID = $crmLead->add($item);

                        $arrUpdate = [
                            'lead' => $ID,
                        ];

                        $statItems->updateItem($item['id'], $arrUpdate);
                    }

                    if ( $bCreateDeal === "Y" AND $bCreateItem === true ) {

                        $ID = $crmDeal->add($item);

                        $arrUpdate = [
                            'deal' => $ID,
                        ];

                        $statItems->updateItem($item['id'], $arrUpdate);

                    }
                }


                if ( $ID > 0 ) {

                    try {

                        $bxId = (int) $item['id_record_bx'];

                        if ( $bxId > 0 ) {

                            $crmActivity = new CRMActivity($userID, $item['phone'], $ID, $bxId);
                            $crmActivity->addCallAndActivity($ownerType, $item);

                        }

                    } catch (Exception $e) {
                        Struct::debug("Something went wrong while creating a Call");
                    }

                }
            }
        }

        return "\SmartCallBack\Cron::createObj();";
    }

    /**
    * write call to B24
    */
    public static function writeCallsToB24() {

        if ( !\CModule::IncludeModule("disk")) {
            Struct::debug("Module disk is not installed");
            return "\SmartCallBack\Cron::writeCallsToB24();";
        }

        $statItems      = new StatItems();
        $arrElements    = $statItems->getWroteCalls();
        $userID         = (int) \COption::GetOptionString(Struct::MODULE_ID, "MAIN_USER_OPTION");

        foreach ( $arrElements as $elem ) {

            if ( !$elem["record_url"] ) {
                continue;
            }

            $file_name = basename($elem["record_url"]); // get file name from url
            $pathToFile = $_SERVER['DOCUMENT_ROOT'] . \SmartCallBack\DownloadItems::DOWNLOAD_DIR . $file_name;

            try {

                $storage = Driver::getInstance()->getStorageByUserId(Struct::USER_ID);
                $folder = $storage->getRootObject();

                if ( !$folder OR !$storage ) {

                    throw new Exception("Folder or storage folder does not exist");
                }

                $fileArray  = \CFile::MakeFileArray($pathToFile);
                $arrData    = ['CREATED_BY' => Struct::USER_ID];

                $file = $folder->uploadFile($fileArray, $arrData);

                if ( $file ) {

                    $fileID = $file->getId();

                    if ( $fileID > 0 ) {

                        $arrUpdate = [
                            'id_record_bx' => $fileID,
                        ];

                        $statItems->updateItem( $elem['id'], $arrUpdate );
                    }

                }


            } catch ( Exception $e ) {
                Struct::debug($e);
            }
        }

        return "\SmartCallBack\Cron::writeCallsToB24();";
    }
}