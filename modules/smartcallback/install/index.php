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

    function smartcallback() {
        $arModuleVersion = array();

        include(substr(__FILE__, 0,  -10)."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = "SmartCallBack";
        $this->MODULE_DESCRIPTION = "Модуль для интеграции с SmartCallBack";

    }

    function InstallFiles($arParams = array()) {

        if ($_ENV["COMPUTERNAME"]!='BX') {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/components/smartcallback/install/components", $_SERVER["DOCUMENT_ROOT"]."/local/components/components", true, true);
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
    }

    function DoUninstall() {

        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

}