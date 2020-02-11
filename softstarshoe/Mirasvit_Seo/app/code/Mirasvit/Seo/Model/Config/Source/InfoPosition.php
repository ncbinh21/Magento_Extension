<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Source;

use Mirasvit\Seo\Api\Config\InfoInterface as InfoConfig;
use Magento\Framework\Option\ArrayInterface;

class InfoPosition implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => InfoConfig::INFO_POSITION_RIGHT, 'label' => __('Right')],
            ['value' => InfoConfig::INFO_POSITION_LEFT, 'label' => __('Left')],
        ];
    }
}
