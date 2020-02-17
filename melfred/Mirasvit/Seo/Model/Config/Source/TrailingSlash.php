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



namespace Mirasvit\Seo\Model\Config\Source;

use Mirasvit\Seo\Model\Config as Config;
use Magento\Framework\Option\ArrayInterface;

class TrailingSlash implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => Config::TRAILING_SLASH, 'label' => __('Redirect to same page with Trailing Slash')],
            ['value' => Config::NO_TRAILING_SLASH, 'label' => __('Redirect to same page without Trailing Slash')],
        ];
    }
}
