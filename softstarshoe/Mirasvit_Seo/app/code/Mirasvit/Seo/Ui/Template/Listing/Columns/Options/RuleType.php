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



namespace Mirasvit\Seo\Ui\Template\Listing\Columns\Options;

use Mirasvit\Seo\Model\Config as Config;

class RuleType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @param bool $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        $arr[] = ['value' => Config::PRODUCTS_RULE, 'label' => __('Products')];
        $arr[] = ['value' => Config::CATEGORIES_RULE, 'label' => __('Categories')];
        $arr[] = ['value' => Config::RESULTS_LAYERED_NAVIGATION_RULE, 'label' => __('Layered navigation')];

        return $arr;
    }
}
