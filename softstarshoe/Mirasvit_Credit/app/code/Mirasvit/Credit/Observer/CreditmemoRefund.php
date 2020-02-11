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

class CreditmemoRefund implements ObserverInterface
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Mirasvit\Credit\Helper\Data
     */
    protected $creditData;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory $balanceFactory
     * @param \Mirasvit\Credit\Helper\Data          $creditData
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Mirasvit\Credit\Helper\Data $creditData
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->creditData = $creditData;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseCreditAmount()) {
            if ($creditmemo->getRefundCredit()) {
                $baseAmount = $creditmemo->getBaseCreditAmount();
                $amount = $creditmemo->getCreditAmount();

                $creditmemo->setBaseCreditTotalRefunded($creditmemo->getBaseCreditTotalRefunded() + $baseAmount);
                $creditmemo->setCreditTotalRefunded($creditmemo->getCreditTotalRefunded() + $amount);
            }

            $order->setBaseCreditRefunded(
                $order->getBaseCreditRefunded() + $creditmemo->getBaseCreditAmount()
            );

            $order->setCreditRefunded(
                $order->getCreditRefunded() + $creditmemo->getCreditAmount()
            );

            if ($order->getCreditInvoiced() > 0
                && $order->getCreditInvoiced() == $order->getCreditRefunded()
                && $order->getTotalInvoiced() == $order->getTotalRefunded()
            ) {
                $order->setForcedCanCreditmemo(false);
            }
        }
    }
}
