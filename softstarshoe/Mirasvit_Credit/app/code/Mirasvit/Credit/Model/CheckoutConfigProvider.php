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


namespace Mirasvit\Credit\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Mirasvit\Credit\Helper\Data as CreditHelper;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $currencyHelper;

    /**
     * @var CreditHelper
     */
    protected $creditHelper;

    /**
     * @param \Magento\Framework\Pricing\Helper\Data $currencyHelper
     * @param CreditHelper                           $creditHelper
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $currencyHelper,
        CreditHelper $creditHelper
    ) {
        $this->currencyHelper = $currencyHelper;
        $this->creditHelper = $creditHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->creditHelper->getQuote();
        $amount = $this->currencyHelper->currency($this->creditHelper->getBalance()->getAmount(), false, false);

        return [
            'creditConfig' => [
                'isAllowed'  => $this->creditHelper->isAllowedForQuote(),
                'amount'     => $amount,
                'amountUsed' => $quote->getUseCredit() == Config::USE_CREDIT_YES,
            ],
        ];
    }
}
