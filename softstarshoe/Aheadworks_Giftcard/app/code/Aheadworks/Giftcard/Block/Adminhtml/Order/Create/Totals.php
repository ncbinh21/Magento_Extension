<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Adminhtml\Order\Create;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals;

/**
 * Class Totals
 *
 * @package Aheadworks\Giftcard\Block\Adminhtml\Order\Create
 */
class Totals extends DefaultTotals
{
    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $values = [];
        $giftcards = $this->getTotal()->getAwGiftcardCodes();
        /** @var QuoteInterface $giftcard */
        foreach ($giftcards as $giftcard) {
            if ($giftcard->isRemove()) {
                continue;
            }
            $values[] = [
                'code' => $giftcard->getGiftcardCode(),
                'label' => 'Gift Card (%1)',
                'amount' => $giftcard->getGiftcardAmount()
            ];
        }
        return $values;
    }
}
