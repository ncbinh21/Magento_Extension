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

class CreditmemoSaveAfter implements ObserverInterface
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Mirasvit\Credit\Helper\Data
     */
    protected $creditData;

    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ProductOptionCreditFactory $productOptionCredit,
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Mirasvit\Credit\Helper\Data $creditData
    ) {
        $this->optionHelper        = $optionHelper;
        $this->productOptionCredit = $productOptionCredit;
        $this->balanceFactory      = $balanceFactory;
        $this->creditData          = $creditData;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        $this->refundCreditProduct($creditmemo);

        if ($creditmemo->getAutomaticallyCreated()) {
            if ($this->creditData->isAutoRefundEnabled()) {
                $creditmemo->setCreditRefundFlag(true)
                    ->setCreditTotalRefunded($creditmemo->getCreditAmount())
                    ->setBaseCreditTotalRefunded($creditmemo->getBaseCreditAmount());
            } else {
                return;
            }
        }

        $creditReturnMax = floatval($creditmemo->getCreditReturnMax());

        if (round($creditmemo->getCreditTotalRefunded(), 2) > round($creditReturnMax + $order->getCreditAmount(), 2)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Store credit amount cannot exceed order amount.')
            );
        }

        if ($creditmemo->getCreditRefundFlag() && $creditmemo->getBaseCreditTotalRefunded()) {
            $order->setBaseCreditTotalRefunded(
                $order->getBaseCreditTotalRefunded() + $creditmemo->getBaseCreditTotalRefunded()
            );
            $order->setCreditTotalRefunded(
                $order->getCreditTotalRefunded() + $creditmemo->getCreditTotalRefunded()
            );

            $balance = $this->balanceFactory->create()->loadByCustomer($order->getCustomerId());
            $balance->addTransaction(
                $creditmemo->getBaseCreditTotalRefunded(),
                \Mirasvit\Credit\Model\Transaction::ACTION_REFUNDED,
                ['order' => $order, 'creditmemo' => $creditmemo]
            );
        }

        if ($order->getBaseCreditTotalRefunded() > 0) {
            $order->setTotalRefunded(
                $order->getTotalRefunded() - ($order->getCreditTotalRefunded() - $order->getCreditRefunded())
            );
            $order->setBaseTotalRefunded(
                $order->getBaseTotalRefunded() - (
                    $order->getBaseCreditTotalRefunded() - $order->getBaseCreditRefunded()
                )
            );
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    public function refundCreditProduct($creditmemo)
    {
        $order = $creditmemo->getOrder();

        $credits = 0;
        /** @var \Magento\Sales\Model\Order\Creditmemo\Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $order->getItemById($item->getOrderItemId());
            if ($orderItem->getProductType() == \Mirasvit\Credit\Model\Product\Type::TYPE_CREDITPOINTS
                || $orderItem->getRealProductType() == \Mirasvit\Credit\Model\Product\Type::TYPE_CREDITPOINTS
            ) {
                $options = $orderItem->getProductOptionByCode('info_buyRequest');
                $option  = $this->productOptionCredit->create();
                $value   = !empty($options['creditOption']) ? $options['creditOption'] : 0;
                $data    = !empty($options['creditOptionData']) ? $options['creditOptionData'] : [];
                $option->setData($data);
                $productCredits = $this->optionHelper->getOptionCredits($option, $value);
                $credits += $productCredits * $item->getQty();
            }
        }
        if ($credits) {
            $balance = $this->balanceFactory->create()
                ->loadByCustomer($order->getCustomerId());

            $balance->addTransaction(
                -1 * $credits,
                \Mirasvit\Credit\Model\Transaction::ACTION_REFUNDED,
                ['order' => $order, 'creditmemo' => $creditmemo]
            );
        }
    }
}
