<?php
$modulesAutoloaderPath = $_SERVER['DOCUMENT_ROOT'] . '/local/firstbit/modules/autoload.php';

if (file_exists($modulesAutoloaderPath)) {
    require_once $modulesAutoloaderPath;
}

AddEventHandler("crm", "OnAfterCrmControlPanelBuild", Array("modules\beldev\CrmControlMenuAdder", "setControlPanelButton"));