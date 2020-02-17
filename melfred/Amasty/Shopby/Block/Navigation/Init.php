<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Block\Navigation;

use Magento\Framework\View\Element\Template;

/**
 * @api
 */
class Init extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        Template\Context $context,
        \Amasty\ShopbyBase\Model\Category\Manager\Proxy $categoryManager,
        array $data = []
    ) {
        $categoryManager->init();
        return parent::__construct($context, $data);
    }
}
