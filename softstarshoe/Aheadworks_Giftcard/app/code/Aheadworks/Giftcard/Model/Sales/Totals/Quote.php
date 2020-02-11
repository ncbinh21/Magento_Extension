<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Sales\Totals;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard as ResourceGiftCard;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote as ModelQuote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Class Quote
 *
 * @package Aheadworks\Giftcard\Model\Sales\Totals
 */
class Quote extends AbstractTotal
{
    /**
     * @var bool
     */
    private $isFirstTimeResetRun = true;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(
        ModelQuote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();
        $this->reset($total, $quote, $address);

        if (!count($items)) {
            return $this;
        }

        $baseGrandTotal = $total->getBaseGrandTotal();
        $grandTotal = $total->getGrandTotal();

        if (!$quote->getExtensionAttributes()
            || ($quote->getExtensionAttributes() && !$quote->getExtensionAttributes()->getAwGiftcardCodes())
            || !$baseGrandTotal
        ) {
            $this->reset($total, $quote, $address, true);
            return $this;
        }

        $baseTotalGiftcardAmount = $totalGiftcardAmount = 0;
        $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
        /** @var $giftcard GiftcardQuoteInterface */
        foreach ($giftcards as $giftcard) {
            if ($giftcard->isRemove()) {
                continue;
            }
            $baseGiftcardUsedAmount = min($giftcard->getGiftcardBalance(), $baseGrandTotal);
            $baseGrandTotal -= $baseGiftcardUsedAmount;

            $giftcardUsedAmount = min($this->priceCurrency->convert($baseGiftcardUsedAmount), $grandTotal);
            $grandTotal -= $giftcardUsedAmount;

            $baseTotalGiftcardAmount += $baseGiftcardUsedAmount;
            $totalGiftcardAmount += $giftcardUsedAmount;

            if ($baseGiftcardUsedAmount <= 0) {
                $giftcard->setIsRemove(true);
            } else {
                $giftcard
                    ->setBaseGiftcardAmount($baseGiftcardUsedAmount)
                    ->setGiftcardAmount($giftcardUsedAmount);
            }
        }
        $this
            ->_addBaseAmount($baseTotalGiftcardAmount)
            ->_addAmount($totalGiftcardAmount);
        $total
            ->setBaseAwGiftcardAmount($baseTotalGiftcardAmount)
            ->setAwGiftcardAmount($totalGiftcardAmount)
            ->setBaseGrandTotal($total->getBaseGrandTotal() - $baseTotalGiftcardAmount)
            ->setGrandTotal($total->getGrandTotal() - $totalGiftcardAmount);
        $quote
            ->setBaseAwGiftcardAmount($baseTotalGiftcardAmount)
            ->setAwGiftcardAmount($totalGiftcardAmount);
        $address
            ->setBaseAwGiftcardAmount($baseTotalGiftcardAmount)
            ->setAwGiftcardAmount($totalGiftcardAmount);

        return $this;
    }

    /**
     * Reset Gift Crad total
     *
     * @param Total $total
     * @param ModelQuote $quote
     * @param AddressInterface $address
     * @param bool $reset
     * @return $this
     */
    private function reset(Total $total, ModelQuote $quote, AddressInterface $address, $reset = false)
    {
        if ($this->isFirstTimeResetRun || $reset) {
            $this->_addAmount(0);
            $this->_addBaseAmount(0);

            $total->setBaseAwGiftcardAmount(0);
            $total->setAwGiftcardAmount(0);

            $quote->setBaseAwGiftcardAmount(0);
            $quote->setAwGiftcardAmount(0);

            $address->setBaseAwGiftcardAmount(0);
            $address->setAwGiftcardAmount(0);

            if ($reset && $quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwGiftcardCodes()) {
                $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
                /** @var $giftcard GiftcardQuoteInterface */
                foreach ($giftcards as $giftcard) {
                    $giftcard->setIsRemove(true);
                }
            }

            $this->isFirstTimeResetRun = false;
        }
        return $this;
    }

    /**
     * Add Gift Card
     *
     * @param ModelQuote $quote
     * @param Total $total
     * @return []
     */
    public function fetch(ModelQuote $quote, Total $total)
    {
        $giftcards = [];
        if ($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwGiftcardCodes()) {
            $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
        }
        if (!empty($giftcards)) {
            return [
                'code' => $this->getCode(),
                'aw_giftcard_codes' => $giftcards,
                'title' => __('Gift Card'),
                'value' => -$total->getAwGiftcardAmount()
            ];
        }

        return null;
    }
}
