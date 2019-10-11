<?php

namespace SmartCallBack;

class DownloadItems {

    const downloadDir = "/upload/scb/"; // dir for saving data from SCB as mp3 files
    private $_chunk_size = 1024*1024;

    /**
     * Downloading file
     *
     * @param $link link to file
     * @param $retbytes
     */
    public function download ( $link, $retbytes = TRUE ) {

        $file_name = basename($link);
        $directory = $_SERVER['DOCUMENT_ROOT'].self::downloadDir;
        $file = $directory . $file_name;

        if ( !is_dir ( $directory ) ){
            mkdir( $directory );
        }

        if ( !is_writable($directory) ) {
            echo "$directory is not readable";
            exit;
        }

        $buffer         = '';
        $cnt            = 0;
        $handle_of      = fopen( $link, 'rb' );
        $handle_wf      = fopen($directory . $file_name, 'wb');

        if ($handle_of === false) {
            return false;
        }

        while ( !feof($handle_of) ) {

            $buffer = fread($handle_of, $this->_chunk_size);
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

}