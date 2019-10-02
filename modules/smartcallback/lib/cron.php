<?php

namespace SmartCallBack;

use Protobuf\Exception;

define('CHUNK_SIZE', 1024*1024); // Size (in bytes) of tiles chunk

class Cron  {

    private $_statTable;
    private $_uploaddir         = "/upload/scb/"; // dir for saving data from SCB as mp3 files
    private $_resForLastDays    = 30;             // get data for last XX days

    public $date_to;
    public $date_from;

    private $_lastItems = []; // contents last items

    function __construct() {

        $this->_statTable = new \SmartCallBack\StatTable;

        $this->date_to    = time();
        $this->date_from  = $this->date_to - $this->_resForLastDays * 60 * 60 ;

        $this->getLastItems();
    }

    /**
     * gets last items for period
     *
     */
    function getLastItems () {

        $res = $this->_statTable->getList([

            'select' => [
                            'query_id',
                        ],
            'filter' => [
                            '>=date_create' => $this->date_from,
                            '<=date_create' => $this->date_to
                        ],
        ]);

        while($item = $res->fetch())  {
            $this->_lastItems[] = $item;
        }

    }

    /**
     * Task for cron write items into database
     *
     */
    public function  cronWriteItems() {

        $POST = Array(
            "date_from" => strtotime($this->date_from),
            "date_to"   => strtotime($this->date_to),
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

//                if ( strlen($item->record_url) > 0 ) {
//                    self::downloadFile($item->record_url);
//                }

                // Write to database


                self::writeItem($item);

            }


        } catch ( Exception $e ) {

            echo 'Caught exeption: ' . $e->getMessage();
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/local/modules/smartcallback/error.log", $e->getMessage());
        }

        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/test.txt", "TEST");
        return "SmartCallBack\Cron::cron();";

    }

    /**
     * Downloading files
     *
     * @param no param
     */
    public function cronDownloadItems () {

        $res = $this->_statTable->getList([
            'select' => array('*'),
            'filter' => array('=record_written' => 0)
        ]);

        while( $item = $res->fetch() ) {
            print_r($item);
        }

    }

    /**
     * Downloading file
     *
     * @param $link link to file
     * @param $retbytes
     */
    public function downloadFile ( $link, $retbytes = TRUE ) {

        $file_name = basename($link);
        $directory = $_SERVER['DOCUMENT_ROOT'].$this->_uploaddir;

        if ( !is_dir ( $directory ) ){
            mkdir( $directory );
        }

        $buffer         = '';
        $cnt            = 0;
        $handle_of      = fopen( $link, 'rb' );
        $handle_wf      = fopen($directory . $file_name, 'wb');

        if ($handle_of === false) {
            return false;
        }

        while (!feof($handle_of)) {

            $buffer = fread($handle_of, CHUNK_SIZE);
            $bytes = fwrite($handle_wf, $buffer);
            ob_flush();
            flush();

            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }

        $status = fclose($handle_of);
        $status_o = fclose($handle_wf);
        $buffer = '';
        $bytes = '';

        if ($retbytes && $status) {
            return $cnt; // return num. bytes delivered like readfile() does.
        }

        return $status;

    }

    /**
     * Write received data
     *
     * @param array $item
     */
    private function writeItem ( $item ) {

        if ( $this->checkIfItemIsExist($item->query_id) === false ) {

            $res = $this->_statTable->add(
                [
                    "query_id"          => $item->query_id,
                    "status_id"         => $item->status->code,
                    "status_title"      => $item->status->title,
                    "type_id"           => $item->type->code,
                    "type_title"        => $item->type->title,
                    "phone"             => $item->phone,
                    "date_create"       => $item->date_create,
                    "utm_source"        => $item->utm_source,
                    "utm_medium"        => $item->utm_medium,
                    "utm_term"          => $item->utm_term,
                    "utm_content"       => $item->utm_content,
                    "utm_updated"       => $item->utm_updated,
                    "record_url"        => $item->record_url,
                    "duration"          => $item->duration
                ]
            );

            if ($res->isSuccess()) {
                print('Added with ID = '.$res->getId());
            }  else  {
                print_r($res->getErrorMessages());
            }

        }

    }

    /**
     * check if id's of element exist in array
     *
     * @param integer $id
     */
    public function checkIfItemIsExist ($id) {

        return array_search( $id,  array_column($this->_lastItems, 'query_id') );

    }

}