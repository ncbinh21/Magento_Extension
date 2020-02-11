<?php

namespace Forix\Custom\Block\Aheadworks\Product;

class ViewCustom extends \Aheadworks\Giftcard\Block\Product\View
{
    public function getGiftcardAmounts()
    {
        $result = [
            [
                'value' => '',
                'label' => __('Select an Amount')
            ]
        ];
        $amountOptions = $this->getAmountOptions();
        foreach ($amountOptions as $option) {
            $result[] = [
                'value' => $option,
                'label' => $this->priceCurrency->convertAndFormat($option, false)
            ];
        }
        if ($this->isAllowOpenAmount()) {
            $result[] = [
                'value' => 'custom',
                'label' => __('Other Amount')
            ];
        }
        return $result;
    }
}