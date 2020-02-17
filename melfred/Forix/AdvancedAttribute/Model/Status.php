<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\AdvancedAttribute\Model;

/**
 * Status
 * @category Forix
 * @package  Forix_AdvancedAttribute
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
            'align_none' => '--',
            'align_left' => __('Left'),
            'align_right' => __('Right'),
            'align_center' => __('Center')
        ];
    }
}
