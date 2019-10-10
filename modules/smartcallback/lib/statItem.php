<?php

namespace SmartCallBack;

class statItems extends StatTable {

    private $_statTable;
    private $_limitFilesToDownload = 10;

    public $date_to;
    public $date_from;
    public $lastItems = []; // contents last items

    function __construct() {

        $this->date_to    = time();
        $this->date_from  = $this->date_to - Struct::resForLastDays * 24 * 60 * 60 ;

        $this->_statTable = new \SmartCallBack\StatTable;
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
            $this->lastItems[] = $item;
        }

    }

    /**
     * Return items which are not downloaded
     *
     * @param no param
     * @return array of items
     */
    public function getItemsToDownload () {

        $arrItems = [];

        $res = $this->_statTable->getList([
            'select' => array('*'),
            'filter' => array('=record_written' => 0),
            'limit' => $this->_limitFilesToDownload,
        ]);

        while( $item = $res->fetch() ) {
            $arrItems[] = $item;
        }

        return $arrItems;
    }

    /**
     * Write received data
     *
     * @param array $item
     */
    public function writeItem ( $item ) {

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
                //print('Added with ID = '.$res->getId());
            }  else  {
                print_r($res->getErrorMessages());
            }

        }

    }

    /**
     * Update item
     *
     * @param $id integer - id of element
     */
    public function updateItem ($id, $array) {

        if ( $id > 0 ) {

            $res = $this->_statTable->update (
                                                $id,
                                                $array
                                             );

            if ($res->isSuccess()) {
                return true;
            } else {
                return false;
            }

        }

    }

    /**
     * check if id's of element exist in array
     *
     * @param integer $id
     */
    public function checkIfItemIsExist ($id) {

        return array_search( $id,  array_column($this->lastItems, 'query_id') );

    }

    /**
     * get items with out liad
     *
     */
    public function getItemsWithOutLead () {

        $arrItems = [];

        $res = $this->_statTable->getList([
            'select' => [ '*' ],
            'filter' => [
                            '=record_written' => 1,
                            '=lead' => 0,
                            '!=id_record_bx' => 0,
                        ],
            'limit' => $this->_limitFilesToDownload,
        ]);

        while( $item = $res->fetch() ) {
            $arrItems[] = $item;
        }

        return $arrItems;
    }

    /**
    * get calls from tables
    *
    */
    public function getWroteCalls () {

        $arrItems = [];

        $res = $this->_statTable->getList([
            'select' => [ '*' ],
            'filter' => [
                '=record_written' => 1,
                '=lead' => 0,
                '=id_record_bx' => 0,
                '!=record_url' => 'NULL'
            ],
            'limit' => $this->_limitFilesToDownload,
        ]);

        while( $item = $res->fetch() ) {
            $arrItems[] = $item;
        }

        return $arrItems;
    }

}

?>