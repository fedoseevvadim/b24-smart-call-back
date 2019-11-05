<?
$module_id = "smartcallback";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($POST_RIGHT>="R") :

    IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
    IncludeModuleLangFile(__FILE__);

    $arAllOptions = array(

        GetMessage("SMART_CALL_BACK"),

        array("CLIENT_TOKEN",                   GetMessage("CLIENT_TOKEN"),                     array("text", 25)),
        array("API_TOKEN",                      GetMessage("API_TOKEN"),                        array("text", 25)),
        array("API_SIGNATURE",                  GetMessage("API_SIGNATURE"),                    array("text", 25)),

        GetMessage("TYPE_OF_OBJECT_TO_CREATE"),

        array("CREATE_LEAD",                    GetMessage("CREATE_LEAD"),                      array("radio", "CREATE_LEAD")),
        array("CREATE_DEAL",                    GetMessage("CREATE_DEAL"),                      array("radio", "CREATE_DEAL")),

        GetMessage("MAIN_USER"),

        array("MAIN_USER_OPTION",               GetMessage("MAIN_USER_OPTION"),                 array("text", 5)),


        GetMessage("LINK_TO_FIELDS"),

        array("UTM_SOURCE",                   GetMessage("UTM_SOURCE"),                         array("text", 25)),
        array("UTM_MEDIUM",                   GetMessage("UTM_MEDIUM"),                         array("text", 25)),
        array("UTM_CAMPAIGN",                 GetMessage("UTM_CAMPAIGN"),                       array("text", 25)),
        array("UTM_TERM",                     GetMessage("UTM_TERM"),                           array("text", 25)),
        array("UTM_CONTENT",                  GetMessage("UTM_CONTENT"),                        array("text", 25)),
        array("UTM_UPDATED",                  GetMessage("UTM_UPDATED"),                        array("text", 25)),
        array("UTM_DOMAIN",                   GetMessage("UTM_DOMAIN"),                         array("text", 25)),


    );

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "subscribe_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
        array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "subscribe_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    if(
        $_SERVER["REQUEST_METHOD"] == "POST"
        && strlen($Update.$Apply.$RestoreDefaults) > 0
        && $POST_RIGHT == "W"
        && check_bitrix_sessid()
    )
    {
        if(strlen($RestoreDefaults)>0)
        {
            COption::RemoveOption("smartcallback");
            $z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
            while($zr = $z->Fetch())
            {
                $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
            }
        }
        else
        {
            foreach($arAllOptions as $arOption)
            {
                $name = $arOption[0];
                if($arOption[2][0]=="text-list")
                {
                    $val = "";
                    foreach($_POST[$name] as $postValue)
                    {
                        $postValue = trim($postValue);
                        if(strlen($postValue) > 0)
                            $val .= ($val <> ""? ",": "").$postValue;
                    }
                }
                else
                {
                    $val = $_POST[$name];
                }

                if($arOption[2][0] == "checkbox" && $val <> "Y")
                    $val = "N";

                if($arOption[2][0] == "radio" ) {

                    if ( $arOption[0] === $_POST["crmObject"] ) {
                        $val = "Y";
                    } else {
                        $val = "N";
                    }

                }


                if($name != "mail_additional_parameters" || $USER->IsAdmin())
                    COption::SetOptionString($module_id, $name, $val);
            }
        }


        $Update = $Update.$Apply;
        ob_start();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
        ob_end_clean();

        if(strlen($_REQUEST["back_url_settings"]) > 0)
        {
            if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
                LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
            else
                LocalRedirect($_REQUEST["back_url_settings"]);
        }
        else
        {
            LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
        }
    }

    ?>
    <form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();

        foreach($arAllOptions as $Option) {
            $type = $Option[2];
            $val = COption::GetOptionString($module_id, $Option[0]);

            if (!is_array($Option)) {
                ?><tr class="heading"><td colspan="2"><?=htmlspecialcharsbx($Option); ?></td></tr><?
            } else {

                ?>
                <tr>
                    <td width="40%" <?if($type[0]=="textarea" || $type[0]=="text-list") echo 'class="adm-detail-valign-top"'?>>
                        <label for="<?echo htmlspecialcharsbx($Option[0])?>"><?echo $Option[1]?></label>
                    <td width="60%">
                        <?
                        if($type[0]=="checkbox")
                        {
                            ?><input type="checkbox" name="<?echo htmlspecialcharsbx($Option[0])?>" id="<?echo htmlspecialcharsbx($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>><?
                        }
                        elseif($type[0]=="radio") {
                            ?>

                            <input type="radio" name="crmObject" value="<?echo htmlspecialcharsbx($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>

                            <?
                        }
                        elseif($type[0]=="text")
                        {
                            ?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($Option[0])?>"><?
                        }
                        elseif($type[0]=="textarea")
                        {
                            ?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialcharsbx($Option[0])?>"><?echo htmlspecialcharsbx($val)?></textarea><?
                        }
                        elseif($type[0]=="text-list")
                        {
                            $aVal = explode(",", $val);
                            foreach($aVal as $val)
                            {
                                ?><input type="text" size="<?echo $type[2]?>" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($Option[0])."[]"?>"><br><?
                            }
                            for($j=0; $j<$type[1]; $j++)
                            {
                                ?><input type="text" size="<?echo $type[2]?>" value="" name="<?echo htmlspecialcharsbx($Option[0])."[]"?>"><br><?
                            }
                        }
                        elseif($type[0]=="selectbox")
                        {
                            ?><select name="<?echo htmlspecialcharsbx($Option[0])?>"><?
                            foreach($type[1] as $optionValue => $optionDisplay)
                            {
                                ?><option value="<?echo $optionValue?>"<?if($val==$optionValue)echo" selected"?>><?echo htmlspecialcharsbx($optionDisplay)?></option><?
                            }
                            ?></select><?
                        }
                        ?></td>
                </tr>
                <?
            }
        }
        ?>
        <?$tabControl->BeginNextTab();?>
        <?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
        <?$tabControl->Buttons();?>
        <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
        <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
        <?if(strlen($_REQUEST["back_url_settings"])>0):?>
            <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
            <input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
        <?endif?>
        <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
        <?=bitrix_sessid_post();?>
        <?$tabControl->End();?>
    </form>
<?endif;?>
