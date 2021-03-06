<?php
global $MESS;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class smartcallback extends CModule {

    var $MODULE_ID = "smartcallback";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME  = "smartcallback";
    var $pathToDB = "/local/modules/smartcallback/install/db/";
    var $executeTime = 60; // sec
    var $userId = 1;

    function smartcallback() {
        $arModuleVersion = array();

        include(substr(__FILE__, 0,  -10)."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = "SmartCallBack";
        $this->MODULE_DESCRIPTION = "Модуль для интеграции с SmartCallBack";

    }

    function InstallDB($arParams = array()) {

        global $DB, $APPLICATION;
        $this->errors = false;

        // Database tables creation
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"].$this->pathToDB.strtolower($DB->type)."/install.sql");

        $time = time();
        $dateTime       = date("d.m.Y H:i:s", time() + 300);
        $dateTimeObj    = date("d.m.Y H:i:s", time() + 300);
        $dateTimeB24    = date("d.m.Y H:i:s", time() + 300);

        \CAgent::AddAgent("\SmartCallBack\Cron::writeItems();", $this->MODULE_ID, $period= "Y", $this->executeTime, "", "Y", "","", $this->userId );
        \CAgent::AddAgent("\SmartCallBack\Cron::DownloadItems();", $this->MODULE_ID, $period= "Y", $this->executeTime, "", "Y", $dateTime,"", $this->userId );
        \CAgent::AddAgent("\SmartCallBack\Cron::createObj();", $this->MODULE_ID, $period= "Y", $this->executeTime, "", "Y", $dateTimeObj,"", $this->userId );
        \CAgent::AddAgent("\SmartCallBack\Cron::writeCallsToB24();", $this->MODULE_ID, $period= "Y", $this->executeTime, "", "Y", $dateTimeB24,"", $this->userId );

//        \COption::GetOptionString($this->MODULE_ID, "CLIENT_TOKEN");
//        \COption::GetOptionString($this->MODULE_ID, "CLIENT_TOKEN");

        if($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $this->errors = false;

        if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
        {
            $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"].$this->pathToDB.strtolower($DB->type)."/uninstall.sql");

        }

        \CAgent::RemoveAgent($this->MODULE_ID, $this->MODULE_ID);

        UnRegisterModule("smartcallback");

        if($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }

        return true;
    }

    function InstallFiles($arParams = array()) {

        if ($_ENV["COMPUTERNAME"]!='BX') {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/smartcallback/install/components", $_SERVER["DOCUMENT_ROOT"]."/local/components/", true, true);
        }
        return true;
    }

    function UnInstallFiles() {

        if ($_ENV["COMPUTERNAME"]!='BX') {
            DeleteDirFilesEx("/local/components/smartcallback/");
        }

        return true;
    }

    function DoInstall() {

        RegisterModule($this->MODULE_ID);
        $this->InstallFiles();
        $this->InstallDB();
    }

    function DoUninstall() {

        $this->UnInstallFiles();
        $this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

}