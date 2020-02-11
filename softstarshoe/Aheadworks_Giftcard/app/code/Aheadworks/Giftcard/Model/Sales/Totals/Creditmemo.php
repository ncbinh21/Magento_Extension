<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Sales\Totals;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo as ModelCreditmemo;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface as GiftcardOrderInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface as GiftcardInvoiceInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface as GiftcardCreditmemoInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterfaceFactory as GiftcardCreditmemoInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Creditmemo
 *
 * @package Aheadworks\Giftcard\Model\Sales\Totals
 */
class Creditmemo extends AbstractTotal
{
    /**
     * @var CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    /**
     * @var GiftcardCreditmemoInterfaceFactory
     */
    private $giftcardCreditmemoFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param CreditmemoExtensionFactory $creditmemoExtensionFactory
     * @param GiftcardCreditmemoInterfaceFactory $giftcardCreditmemoFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param array $data
     */
    public function __construct(
        CreditmemoExtensionFactory $creditmemoExtensionFactory,
        GiftcardCreditmemoInterfaceFactory $giftcardCreditmemoFactory,
        DataObjectHelper $dataObjectHelper,
        $data = []
    ) {
        parent::__construct($data);
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
        $this->giftcardCreditmemoFactory = $giftcardCreditmemoFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param ModelCreditmemo $creditmemo
     * @return $this
     */
    public function collect(ModelCreditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $creditmemo->setAwGiftcardAmount(0);
        $creditmemo->setBaseAwGiftcardAmount(0);

        $order = $creditmemo->getOrder();
        if ($order->getBaseAwGiftcardAmount()
            && $order->getBaseAwGiftcardRefunded() != $order->getBaseAwGiftcardAmount()
            && $order->getExtensionAttributes() && $order->getExtensionAttributes()->getAwGiftcardCodesInvoiced()
        ) {
            $baseTotalGiftcardAmount = $totalGiftcardAmount = 0;
            $baseGrandTotal = $creditmemo->getBaseGrandTotal();
            $grandTotal = $creditmemo->getGrandTotal();

            $extensionAttributes = $creditmemo->getExtensionAttributes()
                ? $creditmemo->getExtensionAttributes()
                : $this->creditmemoExtensionFactory->create();
            $orderGiftcards = $order->getExtensionAttributes()->getAwGiftcardCodes();
            $invoicedGiftcards = $order->getExtensionAttributes()->getAwGiftcardCodesInvoiced();
            $refundedGiftcards = $order->getExtensionAttributes()->getAwGiftcardCodesRefunded() ? : [];

            $toRefundGiftcards = [];
            /** @var GiftcardOrderInterface $orderGiftcard */
            foreach ($orderGiftcards as $orderGiftcard) {
                /** @var GiftcardCreditmemoInterface $toRefundGiftcard */
                $toRefundGiftcard = $this->giftcardCreditmemoFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $toRefundGiftcard,
                    $orderGiftcard->getData(),
                    GiftcardCreditmemoInterface::class
                );
                $toRefundGiftcard->setId(null);
                $toRefundGiftcard->setBaseGiftcardAmount(0);
                $toRefundGiftcard->setGiftcardAmount(0);
                $toRefundGiftcard->setOrderId($creditmemo->getOrderId());

                /** @var GiftcardInvoiceInterface $invoicedGiftcard */
                foreach ($invoicedGiftcards as $invoicedGiftcard) {
                    if ($toRefundGiftcard->getGiftcardId() == $invoicedGiftcard->getGiftcardId()) {
                        $toRefundGiftcard->setBaseGiftcardAmount(
                            $toRefundGiftcard->getBaseGiftcardAmount() + $invoicedGiftcard->getBaseGiftcardAmount()
                        );
                        $toRefundGiftcard->setGiftcardAmount(
                            $toRefundGiftcard->getGiftcardAmount() + $invoicedGiftcard->getGiftcardAmount()
                        );
                    }
                }

                /** @var GiftcardCreditmemoInterface $refundedGiftcard */
                foreach ($refundedGiftcards as $refundedGiftcard) {
                    if ($toRefundGiftcard->getGiftcardId() == $refundedGiftcard->getGiftcardId()) {
                        $toRefundGiftcard->setBaseGiftcardAmount(
                            $toRefundGiftcard->getBaseGiftcardAmount() - $refundedGiftcard->getBaseGiftcardAmount()
                        );
                        $toRefundGiftcard->setGiftcardAmount(
                            $toRefundGiftcard->getGiftcardAmount() - $refundedGiftcard->getGiftcardAmount()
                        );
                    }
                }
                $baseGiftcardUsedAmount = min($toRefundGiftcard->getBaseGiftcardAmount(), $baseGrandTotal);
                $baseGrandTotal -= $baseGiftcardUsedAmount;

                $giftcardUsedAmount = min($toRefundGiftcard->getGiftcardAmount(), $grandTotal);
                $grandTotal -= $giftcardUsedAmount;

                $baseTotalGiftcardAmount += $baseGiftcardUsedAmount;
                $totalGiftcardAmount += $giftcardUsedAmount;

                $toRefundGiftcard->setBaseGiftcardAmount($baseGiftcardUsedAmount);
                $toRefundGiftcard->setGiftcardAmount($giftcardUsedAmount);

                if ($toRefundGiftcard->getBaseGiftcardAmount() > 0) {
                    $toRefundGiftcards[] = $toRefundGiftcard;
                }
            }

            if ($baseTotalGiftcardAmount > 0) {
                $extensionAttributes->setAwGiftcardCodes($toRefundGiftcards);
                $creditmemo->setExtensionAttributes($extensionAttributes);

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalGiftcardAmount);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalGiftcardAmount);

                $creditmemo->setBaseAwGiftcardAmount($baseTotalGiftcardAmount);
                $creditmemo->setAwGiftcardAmount($totalGiftcardAmount);

                $order->setBaseAwGiftcardRefunded(
                    $order->getBaseAwGiftcardRefunded() + $creditmemo->getBaseAwGiftcardAmount()
                );
                $order->setAwGiftcardRefunded(
                    $order->getAwGiftcardRefunded() + $creditmemo->getAwGiftcardAmount()
                );
            }
        }
        return $this;
    }
}
