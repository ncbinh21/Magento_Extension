<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 17/08/2018
 * Time: 16:08
 */

namespace Forix\Custom\Model\Import;

class Location extends \Amasty\Storelocator\Model\Import\Location
{
    protected $locationColumnNames = [
        self::COL_ID,
        self::COL_NAME,
        self::COL_COUNTRY,
        self::COL_CITY,
        self::COL_ZIP,
        self::COL_ADDRESS,
        self::COL_STATE,
        self::COL_DESCRIPTION,
        self::COL_PHONE,
        self::COL_EMAIL,
        self::COL_STORES,
        self::COL_STATUS,
        self::COL_PHOTO,
        self::MARKER,
        self::COL_WEBSITE,
        self::COL_LAT,
        self::COL_LNG,
        self::COL_POSITION,
        self::COL_SHOW_SCHEDULE,
        'toll_free_phone',
        'office_phone',
        'fax',
        'contact',
        'contact_area',
        'contact_district',
        'contact_phone',
        'contact_email',
        'contact_two',
        'contact_district_two',
        'contact_phone_two',
        'contact_email_two',
    ];
}