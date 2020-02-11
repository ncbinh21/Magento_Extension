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


namespace Mirasvit\Credit\Plugin\Checks;

use Mirasvit\Credit\Model\Config;

class ZeroTotal
{

    /**
     * @param \Magento\Payment\Model\Checks\ZeroTotal      $subject
     * @param \callable                                    $proceed
     * @param \Magento\Payment\Model\Method\AbstractMethod $paymentMethod
     * @param \Magento\Quote\Model\Quote                   $quote
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsApplicable(
        \Magento\Payment\Model\Checks\ZeroTotal $subject, $proceed, $paymentMethod, \Magento\Quote\Model\Quote $quote
    ) {
        $result = $proceed($paymentMethod, $quote);
        if ($quote->getBaseGrandTotal() < 0.0001 && $quote->getUseCredit() == Config::USE_CREDIT_YES) {
            return true;
        }

        return $result;
    }
}