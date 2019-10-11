<?php
//namespace Bitrix\Stat;
namespace SmartCallBack;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class StatTable
 *
 * Fields:
 * <ul>
 * <li> id int mandatory
 * <li> query_id int optional
 * <li> status_id int optional
 * <li> status_title string(255) optional
 * <li> type_id int optional
 * <li> type_title string(255) optional
 * <li> phone string(255) optional
 * <li> date_create string(255) optional
 * <li> utm_source string(255) optional
 * <li> utm_medium string(255) optional
 * <li> utm_campaign string(255) optional
 * <li> utm_term string(255) optional
 * <li> utm_content string(255) optional
 * <li> utm_updated string(255) optional
 * <li> record_url string(255) optional
 * <li> duration int optional
 * <li> record_written int optional
 * </ul>
 *
 * @package Bitrix\Stat
 **/

class StatTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'scb_stat';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'id' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('STAT_ENTITY_ID_FIELD'),
            ),
            'query_id' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_QUERY_ID_FIELD'),
            ),
            'status_id' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_STATUS_ID_FIELD'),
            ),
            'status_title' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateStatusTitle'),
                'title' => Loc::getMessage('STAT_ENTITY_STATUS_TITLE_FIELD'),
            ),
            'type_id' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_TYPE_ID_FIELD'),
            ),
            'type_title' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateTypeTitle'),
                'title' => Loc::getMessage('STAT_ENTITY_TYPE_TITLE_FIELD'),
            ),
            'phone' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validatePhone'),
                'title' => Loc::getMessage('STAT_ENTITY_PHONE_FIELD'),
            ),
            'date_create' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateDateCreate'),
                'title' => Loc::getMessage('STAT_ENTITY_DATE_CREATE_FIELD'),
            ),
            'utm_source' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUtmSource'),
                'title' => Loc::getMessage('STAT_ENTITY_UTM_SOURCE_FIELD'),
            ),
            'utm_medium' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUtmMedium'),
                'title' => Loc::getMessage('STAT_ENTITY_UTM_MEDIUM_FIELD'),
            ),
            'utm_campaign' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUtmCampaign'),
                'title' => Loc::getMessage('STAT_ENTITY_UTM_CAMPAIGN_FIELD'),
            ),
            'utm_term' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUtmTerm'),
                'title' => Loc::getMessage('STAT_ENTITY_UTM_TERM_FIELD'),
            ),
            'utm_content' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUtmContent'),
                'title' => Loc::getMessage('STAT_ENTITY_UTM_CONTENT_FIELD'),
            ),
            'utm_updated' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateUtmUpdated'),
                'title' => Loc::getMessage('STAT_ENTITY_UTM_UPDATED_FIELD'),
            ),
            'record_url' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateRecordUrl'),
                'title' => Loc::getMessage('STAT_ENTITY_RECORD_URL_FIELD'),
            ),
            'duration' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_DURATION_FIELD'),
            ),
            'record_written' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_RECORD_WRITTEN_FIELD'),
            ),
            'lead' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_LEAD_FIELD'),
            ),
            'deal' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_DEAL_FIELD'),
            ),
            'id_record_bx' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('STAT_ENTITY_ID_RECORD_BX_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for status_title field.
     *
     * @return array
     */
    public static function validateStatusTitle()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for type_title field.
     *
     * @return array
     */
    public static function validateTypeTitle()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for phone field.
     *
     * @return array
     */
    public static function validatePhone()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for date_create field.
     *
     * @return array
     */
    public static function validateDateCreate()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for utm_source field.
     *
     * @return array
     */
    public static function validateUtmSource()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for utm_medium field.
     *
     * @return array
     */
    public static function validateUtmMedium()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for utm_campaign field.
     *
     * @return array
     */
    public static function validateUtmCampaign()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for utm_term field.
     *
     * @return array
     */
    public static function validateUtmTerm()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for utm_content field.
     *
     * @return array
     */
    public static function validateUtmContent()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for utm_updated field.
     *
     * @return array
     */
    public static function validateUtmUpdated()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for record_url field.
     *
     * @return array
     */
    public static function validateRecordUrl()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
}