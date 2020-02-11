<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Navigation\Top;

/**
 * @api
 */
class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    public function getActiveFilters()
    {
        return [];
    }
}
