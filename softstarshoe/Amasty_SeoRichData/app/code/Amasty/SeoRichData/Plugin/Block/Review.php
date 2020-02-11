<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Plugin\Block;

use Amasty\SeoRichData\Model\DataCollector;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Review
{
    public function afterToHtml(
        $subject,
        $result
    ) {
        $result = preg_replace('|itemprop=".*"|U', '', $result);
        $result = preg_replace('|itemtype=".*"|U', '', $result);
        $result = str_replace('itemscope', '', $result);

        return $result;
    }
}

