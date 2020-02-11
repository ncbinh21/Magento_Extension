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


namespace Mirasvit\Credit\Service\Config;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\Credit\Api\Config\CalculationConfigInterface;

class CalculationConfig implements CalculationConfigInterface
{
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function isTaxIncluded($store = null)
    {
        return $this->scopeConfig->getValue(
            'credit/calculation/include_tax',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function IsShippingIncluded($store = null)
    {
        return $this->scopeConfig->getValue(
            'credit/calculation/include_shipping',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}