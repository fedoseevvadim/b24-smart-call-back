<?php

namespace SmartCallBack;

define('CHUNK_SIZE', 1024*1024); // Size (in bytes) of tiles chunk

class Cron  {

    private $_statTable;
    private $_uploaddir = "/upload/";

    function __construct() {

        $this->_statTable = new \SmartCallBack\StatTable;

    }

    /**
     * Downloading file
     *
     * @param $link link to file
     * @param $retbytes
     */
    public function downloadFile ( $link, $retbytes = TRUE ) {

        $file_name = basename($link);

        $buffer         = '';
        $cnt            = 0;
        $handle_of      = fopen( $link, 'rb' );
        $handle_wf      = fopen($_SERVER['DOCUMENT_ROOT'].$this->_uploaddir.$file_name, 'wb');

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
    public function writeItem ( $item ) {

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
                "duration"          => $item->duration,
                "record_written"    => $item->record_written,
            ]
        );

        if($res->isSuccess())  {
            print('Added with ID = '.$res->getId());
        }  else  {
            print_r($res->getErrorMessages());
        }

    }

}