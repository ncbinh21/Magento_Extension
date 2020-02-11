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


namespace Mirasvit\Credit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Mirasvit\Credit\Api\Config\CalculationConfigInterface;

class Calculation extends AbstractHelper
{
    public function __construct(
        CalculationConfigInterface $calculationConfig,
        Context $context
    ) {
        parent::__construct($context);

        $this->calculationConfig = $calculationConfig;
    }

    /**
     * @param float $credits
     * @param float $tax
     * @param float $shipping
     * @return float
     */
    public function calc($credits, $tax = 0.00, $shipping = 0.00)
    {
        if (!$this->calculationConfig->IsShippingIncluded()) {
            $credits -= $shipping;
        }
        if (!$this->calculationConfig->isTaxIncluded()) {
            $credits -= $tax;
        }

        return $credits;
    }
}
