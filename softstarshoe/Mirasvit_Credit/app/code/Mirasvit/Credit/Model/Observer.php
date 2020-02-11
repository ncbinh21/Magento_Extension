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

class Observer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\Context           $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->context = $context;
    }

    /**
     * @return $this
     */
    public function customerSaveAfter()
    {
        return $this;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function addPaypalCreditItem($observer)
    {
        $payPalCart = $observer->getEvent()->getPaypalCart();

        if ($payPalCart) {
            $salesEntity = $payPalCart->getSalesEntity();
            if ($salesEntity instanceof \Magento\Quote\Model\Quote) {
                $balanceField = 'base_credit_amount_used';
            } elseif ($salesEntity instanceof \Magento\Sales\Model\Order) {
                $balanceField = 'base_credit_amount';
            } else {
                return;
            }

            $value = abs($salesEntity->getDataUsingMethod($balanceField));

            if ($value > 0.0001) {
                $payPalCart->updateTotal(
                    \Magento\Paypal\Model\Cart::TOTAL_DISCOUNT,
                    floatval($value),
                    __(
                        'Store Credit (%1)',
                        $this->storeManager->getStore()->convertPrice($value, true, false)
                    )
                );
            }
        }
    }
}
