<?php

/**
 * Forix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Forix.com license that is
 * available through the world-wide-web at this URL:
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Forix
 * @package     Forix_Bannerslider
 * @copyright   Copyright (c) 2012 Forix (http://www.forixwebdesign.com/)
 * @license
 */

namespace Forix\Bannerslider\Model\ResourceModel\Slider;

/**
 * Slider Collection
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * _contruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\Bannerslider\Model\Slider', 'Forix\Bannerslider\Model\ResourceModel\Slider');
    }
}
