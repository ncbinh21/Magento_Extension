<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Pricing\Price;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Catalog\Pricing\Price\FinalPrice as CatalogFinalPrice;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard\Price as GiftcardProductPrice;

/**
 * Class FinalPrice
 *
 * @package Aheadworks\Giftcard\Pricing\Price
 */
class FinalPrice extends CatalogFinalPrice
{
    /**
     * @var AmountInterface
     */
    protected $maximalPrice;

    /**
     * @var AmountInterface
     */
    protected $minimalPrice;

    /**
     * {@inheritdoc}
     */
    public function getMaximalPrice()
    {
        if ($this->maximalPrice === null) {
            $openAmountMax = $this->getPriceModel()->getOpenAmountMax($this->getProduct());
            $price = false;
            if ($openAmountMax !== false) {
                $price = $openAmountMax;
            }
            $amounts = $this->getPriceModel()->getAmounts($this->getProduct());
            if (!empty($amounts)) {
                if ($price) {
                    $amounts[] = $price;
                }
                $price = max($amounts);
            }
            if ($price) {
                $this->maximalPrice = $this->calculator->getAmount(
                    $this->priceCurrency->convertAndRound($price),
                    $this->getProduct()
                );
            }
        }
        return $this->maximalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimalPrice()
    {
        if ($this->minimalPrice === null) {
            $openAmountMin = $this->getPriceModel()->getOpenAmountMin($this->getProduct());
            $price = false;
            if ($openAmountMin !== false) {
                $price = $openAmountMin;
            }
            $amounts = $this->getPriceModel()->getAmounts($this->getProduct());
            if (!empty($amounts)) {
                if ($price) {
                    $amounts[] = $price;
                }
                $price = min($amounts);
            }
            if ($price) {
                $this->minimalPrice = $this->calculator->getAmount(
                    $this->priceCurrency->convertAndRound($price),
                    $this->getProduct()
                );
            }
        }
        return $this->minimalPrice;
    }

    /**
     * Retrieve product price model
     *
     * @return GiftcardProductPrice
     */
    private function getPriceModel()
    {
        return $this->getProduct()->getPriceModel();
    }
}
