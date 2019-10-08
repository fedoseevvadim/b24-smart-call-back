<?php

namespace SmartCallBack;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Seo\Engine\Bitrix;

class LID {

    private $_userId = 1;


    /**
     * Create a deal
     *
     * @param array array of fields
     */
    public function addDeal (array $array) {

        $res = \Bitrix\Crm\DealTable::add(
            [
                "TITLE" => "Новый лид по звонку с из SmartCallBack",
                "DATE_CREATE" => new DateTime(),
                "DATE_MODIFY" => new DateTime(),
                "CREATED_BY_ID" => $this->_userId,
                "MODIFY_BY_ID" => $this->_userId,
                "ASSIGNED_BY_ID" => $this->_userId,
                "STAGE_SEMANTIC_ID" => P,
                "IS_NEW"    => N,
                "COMPANY_ID" => 0,
                "OPENED"    => N,
                "CATEGORY_ID" => 4,
                "STAGE_ID" => "NEW",
            ]
        );

        if ($res->isSuccess()) {
            print('Added with ID = '.$res->getId());
        }  else  {
            print_r($res->getErrorMessages());
        }

    }


}