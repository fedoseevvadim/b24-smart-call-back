<?php

namespace SmartCallBack;

use Protobuf\Exception;


class Cron  {

    public $date_to;
    public $date_from;

    private $_pathToLog = "/local/modules/smartcallback/error.log";

    function __construct() {

        $this->date_to    = time();
        $this->date_from  = $this->date_to - ( Struct::resForLastDays * 24 * 60 * 60 ) ;

    }

    /**
     * Task for cron write items into database
     *
     */
    public function  writeItems() {

        $statItems = new statItems();

        $POST = Array(
            "date_from" => $this->date_from,
            "date_to"   => $this->date_to,
        );

        try {

            // get API, TOKEN, SIGNATURE
            $CLIENT_TOKEN   = \COption::GetOptionString(Struct::moduleID, "CLIENT_TOKEN");
            $API_TOKEN      = \COption::GetOptionString(Struct::moduleID, "API_TOKEN");
            $API_SIGNATURE  = \COption::GetOptionString(Struct::moduleID, "API_SIGNATURE");

            $SmartCallBackAPI = new API(
                $CLIENT_TOKEN,
                $API_TOKEN,
                $API_SIGNATURE
            );

            $SmartCallBackAPI->getQueryList($POST); // make a query to SCB

            $result = json_decode($SmartCallBackAPI->RESULT); // decode string

            foreach ( $result->response->queries as $item ) {

                $statItems->writeItem($item);

            }

        } catch ( Exception $e ) {

            echo 'Caught exeption: ' . $e->getMessage();
            file_put_contents($_SERVER["DOCUMENT_ROOT"].$this->_pathToLog, $e->getMessage());
        }

        //return "SmartCallBack\Cron::cron();";

    }

    /**
     * Downloading mp3 items
     *
     */
    public function downloadItems() {

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
    }

    /**
     * creating lids in CRM system B24
     *
     */
    public function createLids () {

        $statItems  = new statItems();

        foreach ( $statItems->lastItems as $item ) {

        }

    }


}