<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Sales\Totals;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magento\Sales\Model\Order\Invoice as ModelInvoice;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface as GiftcardOrderInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface as GiftcardInvoiceInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterfaceFactory as GiftcardInvoiceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Invoice
 *
 * @package Aheadworks\Giftcard\Model\Sales\Totals
 */
class Invoice extends AbstractTotal
{
    /**
     * @var InvoiceExtensionFactory
     */
    private $invoiceExtensionFactory;

    /**
     * @var GiftcardInvoiceInterfaceFactory
     */
    private $giftcardInvoiceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param InvoiceExtensionFactory $invoiceExtensionFactory
     * @param GiftcardInvoiceInterfaceFactory $giftcardInvoiceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param array $data
     */
    public function __construct(
        InvoiceExtensionFactory $invoiceExtensionFactory,
        GiftcardInvoiceInterfaceFactory $giftcardInvoiceFactory,
        DataObjectHelper $dataObjectHelper,
        $data = []
    ) {
        parent::__construct($data);
        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
        $this->giftcardInvoiceFactory = $giftcardInvoiceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ModelInvoice $invoice)
    {
        parent::collect($invoice);
        $invoice->setAwGiftcardAmount(0);
        $invoice->setBaseAwGiftcardAmount(0);

        $order = $invoice->getOrder();
        if ($order->getBaseAwGiftcardAmount()
            && $order->getBaseAwGiftcardInvoiced() != $order->getBaseAwGiftcardAmount()
            && $order->getExtensionAttributes() && $order->getExtensionAttributes()->getAwGiftcardCodes()
        ) {
            $baseTotalGiftcardAmount = $totalGiftcardAmount = 0;
            $baseGrandTotal = $invoice->getBaseGrandTotal();
            $grandTotal = $invoice->getGrandTotal();

            $extensionAttributes = $invoice->getExtensionAttributes()
                ? $invoice->getExtensionAttributes()
                : $this->invoiceExtensionFactory->create();
            $orderGiftcards = $order->getExtensionAttributes()->getAwGiftcardCodes();
            $invoicedGiftcards = $order->getExtensionAttributes()->getAwGiftcardCodesInvoiced() ? : [];

            $toInvoiceGiftcards = [];
            /** @var GiftcardOrderInterface $orderGiftcard */
            foreach ($orderGiftcards as $orderGiftcard) {
                /** @var GiftcardInvoiceInterface $toInvoiceGiftcard */
                $toInvoiceGiftcard = $this->giftcardInvoiceFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $toInvoiceGiftcard,
                    $orderGiftcard->getData(),
                    GiftcardInvoiceInterface::class
                );
                $toInvoiceGiftcard->setId(null);
                $toInvoiceGiftcard->setOrderId($invoice->getOrderId());

                /** @var GiftcardInvoiceInterface $invoicedGiftcard */
                foreach ($invoicedGiftcards as $invoicedGiftcard) {
                    if ($toInvoiceGiftcard->getGiftcardId() == $invoicedGiftcard->getGiftcardId()) {
                        $toInvoiceGiftcard->setBaseGiftcardAmount(
                            $toInvoiceGiftcard->getBaseGiftcardAmount() - $invoicedGiftcard->getBaseGiftcardAmount()
                        );
                        $toInvoiceGiftcard->setGiftcardAmount(
                            $toInvoiceGiftcard->getGiftcardAmount() - $invoicedGiftcard->getGiftcardAmount()
                        );
                    }
                }
                $baseGiftcardUsedAmount = min($toInvoiceGiftcard->getBaseGiftcardAmount(), $baseGrandTotal);
                $baseGrandTotal -= $baseGiftcardUsedAmount;

                $giftcardUsedAmount = min($toInvoiceGiftcard->getGiftcardAmount(), $grandTotal);
                $grandTotal -= $giftcardUsedAmount;

                $baseTotalGiftcardAmount += $baseGiftcardUsedAmount;
                $totalGiftcardAmount += $giftcardUsedAmount;

                $toInvoiceGiftcard->setBaseGiftcardAmount($baseGiftcardUsedAmount);
                $toInvoiceGiftcard->setGiftcardAmount($giftcardUsedAmount);

                if ($toInvoiceGiftcard->getBaseGiftcardAmount() > 0) {
                    $toInvoiceGiftcards[] = $toInvoiceGiftcard;
                }
            }

            if ($baseTotalGiftcardAmount > 0) {
                $extensionAttributes->setAwGiftcardCodes($toInvoiceGiftcards);
                $invoice->setExtensionAttributes($extensionAttributes);

                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalGiftcardAmount);
                $invoice->setGrandTotal($invoice->getGrandTotal() - $totalGiftcardAmount);

                $invoice->setBaseAwGiftcardAmount($baseTotalGiftcardAmount);
                $invoice->setAwGiftcardAmount($totalGiftcardAmount);

                $order->setBaseAwGiftcardInvoiced(
                    $order->getBaseAwGiftcardInvoiced() + $invoice->getBaseAwGiftcardAmount()
                );
                $order->setAwGiftcardInvoiced(
                    $order->getAwGiftcardInvoiced() + $invoice->getAwGiftcardAmount()
                );
            }
        }

        return $this;
    }
}
