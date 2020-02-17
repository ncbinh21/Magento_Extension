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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Model\Config\Source;

class Occurence implements \Magento\Framework\Option\ArrayInterface
{
    const FIRST = 1;
    const LAST = 2;
    const RANDOM = 3;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FIRST, 'label' => __('First')],
            ['value' => self::LAST, 'label' => __('Last')],
            ['value' => self::RANDOM, 'label' => __('Random')],
        ];
    }
}
