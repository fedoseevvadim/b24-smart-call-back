<?php


namespace modules\beldev;


use CComponentEngine;

/**
 * Класс для добавления элемента показов в меню CRM
 *
 * Class CrmControlMenuAdder
 * @package modules\beldev
 */
class CrmControlMenuAdder
{
    const CRM_CTRL_PANEL_ITEM_MARKETPLACE = 'Показы';

    /**
     * @param array $fields
     */
    public function setControlPanelButton(array &$fields)
    {
        $fields[] = [
            'ID' => 'DISPLAYS',
            'MENU_ID' => 'menu_crm_displays',
            'NAME' => self::CRM_CTRL_PANEL_ITEM_MARKETPLACE,
            'TITLE' => self::CRM_CTRL_PANEL_ITEM_MARKETPLACE,
            'URL' => CComponentEngine::MakePathFromTemplate('/crm/deal/kanban/category/4/'),
            'ICON' => 'apps',
            'IS_DISABLED' => false,
        ];
    }
}