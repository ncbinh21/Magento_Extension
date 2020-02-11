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

use Mirasvit\Seo\Model\Config as Config;
use Magento\Framework\Option\ArrayInterface;

class MetaTitlePageNumber implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            [
                'value' => Config::META_TITLE_PAGE_NUMBER_BEGIN,
                'label' => __('At the beginning')
            ],
            [
                'value' => Config::META_TITLE_PAGE_NUMBER_END,
                'label' => __('At the end')
            ],
            [
                'value' => Config::META_TITLE_PAGE_NUMBER_BEGIN_FIRST_PAGE,
                'label' => __('At the beginning (add to the first page)')
            ],
            [
                'value' => Config::META_TITLE_PAGE_NUMBER_END_FIRST_PAGE,
                'label' => __('At the end (add to the first page)')
            ],
        ];
    }
}
