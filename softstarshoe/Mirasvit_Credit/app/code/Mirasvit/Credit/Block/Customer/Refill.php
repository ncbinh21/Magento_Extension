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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Credit\Model\Config;

class Refill extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config  $config
     * @param Context $context
     */
    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\Product|false
     */
    public function getRefillProduct()
    {
        return $this->config->getRefillProduct();
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getOptions()
    {
        $product = $this->getRefillProduct();

        return $product->getOptions();
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        if ($this->getRefillProduct()) {
            return true;
        }

        return false;
    }
}