<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model;

/**
 * Status
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Status
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    /**
     * get available statuses.
     *
     * @return []
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled')
            , self::STATUS_DISABLED => __('Disabled'),
        ];
    }

    /**
     * @return array
     */
    public static function getTextAlign() {
        return [
            'a-none' => '--',
            'a-left' => __('Left'),
            'a-right' => __('Right'),
            'a-center' => __('Center')
        ];
    }
}
