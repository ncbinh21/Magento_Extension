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



namespace Mirasvit\Credit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Credit\Model\Config;

abstract class AbstractObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory      $balanceFactory
     * @param \Magento\Framework\Model\Context           $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->context = $context;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Quote\Model\Quote         $quote
     * @param \Magento\Quote\Model\Quote\Payment $payment
     * @param bool                               $isUseCredit
     * @return void
     */
    protected function _importPaymentData($quote, $payment, $isUseCredit)
    {
        if (!$quote || !$quote->getCustomerId()) {
            return;
        }

        $quote->setUseCredit($isUseCredit ? Config::USE_CREDIT_YES : Config::USE_CREDIT_NO);
        if ($isUseCredit) {
            $balance = $this->balanceFactory->create()->loadByCustomer($quote->getCustomerId());
            if ($balance) {
                $quote->setBalanceInstance($balance);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            } else {
                $quote->setUseCredit(Config::USE_CREDIT_NO);
            }
        }
    }
}
