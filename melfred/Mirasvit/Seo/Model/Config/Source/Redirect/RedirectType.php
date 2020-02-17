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



namespace Mirasvit\Seo\Model\Config\Source\Redirect;

class RedirectType implements \Magento\Framework\Option\ArrayInterface
{
    const PERMANENT301 = 301;
    const MOVED302     = 302;
    const TEMPORARY307 = 307;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PERMANENT301, 'label' => __('301 Moved Permanently')],
            ['value' => self::MOVED302,     'label' => __('302 Object Moved')],
            ['value' => self::TEMPORARY307, 'label' => __('307 Temporary Redirect')],
        ];
    }
}