<?php

namespace SmartCallBack;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Seo\Engine\Bitrix;

class LID {

    private $_userId = 1;
    private $_title = "Новый лид по звонку с из SmartCallBack";
    private $_stage = "C4:NEW";
    private $_category = 4;
    private $_isNew = "Y";

    /**
     * Add a deal
     *
     * @param array array of fields
     */
    public function addDeal (array $array) {

        // TODO
        // использовать массив $array

        $res = \Bitrix\Crm\DealTable::add(
            [
                "TITLE" => $this->_title,
                "DATE_CREATE" => new DateTime(),
                "DATE_MODIFY" => new DateTime(),
                "CREATED_BY_ID" => $this->_userId,
                "MODIFY_BY_ID" => $this->_userId,
                "ASSIGNED_BY_ID" => $this->_userId,
                "STAGE_SEMANTIC_ID" => P,
                "IS_NEW"    => $this->_isNew,
                "COMPANY_ID" => 0,
                "OPENED"    => N,
                "CATEGORY_ID" => $this->_category,
                "STAGE_ID" => $this->_stage,
            ]
        );

        if ($res->isSuccess()) {
            //print('Added with ID = '.$res->getId());
            return $res->getId();
        }  else  {
            print_r($res->getErrorMessages());
        }

    }


}