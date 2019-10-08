<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use SmartCallBack\API,
    SmartCallBack\Struct,
    SmartCallBack\Cron;


if ( CModule::IncludeModule("smartcallback")) {

//    $cron = new Cron();
//
//    $POST = Array(
//        "date_from" => strtotime('2019-09-01'),
//        "date_to"   => strtotime('2019-09-26'),
//    );
//
//    $CLIENT_TOKEN   = COption::GetOptionString(Struct::moduleID, "CLIENT_TOKEN");
//    $API_TOKEN      = COption::GetOptionString(Struct::moduleID, "API_TOKEN");
//    $API_SIGNATURE  = COption::GetOptionString(Struct::moduleID, "API_SIGNATURE");
//
//    $SmartCallBackAPI = new API(
//        $CLIENT_TOKEN,
//        $API_TOKEN,
//        $API_SIGNATURE
//    );
//
//    $SmartCallBackAPI->getQueryList($POST);
//
//    $result = json_decode($SmartCallBackAPI->RESULT);
//
//    foreach ( $result->response->queries as $item ) {
//
//        if ( strlen($item->record_url) > 0 ) {
//            $cron->downloadFile($item->record_url);
//        }
//
//        // Пока запишем в базу
//        //$cron->writeItem($item);
//
//    }

}

$this->IncludeComponentTemplate();
?>