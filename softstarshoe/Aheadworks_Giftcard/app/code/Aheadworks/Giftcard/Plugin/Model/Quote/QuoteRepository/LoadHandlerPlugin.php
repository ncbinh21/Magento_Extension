<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model\Quote\QuoteRepository;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository\LoadHandler;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote\CollectionFactory as GiftcardQuoteCollectionFactory;
use Magento\Framework\Data\Collection;

/**
 * Class LoadHandlerPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model\Quote\QuoteRepository
 */
class LoadHandlerPlugin
{
    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var GiftcardQuoteCollectionFactory
     */
    private $giftcardQuoteCollectionFactory;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->giftcardQuoteCollectionFactory = $giftcardQuoteCollectionFactory;
    }

    /**
     * Add Gift Card codes to quote extension attribute
     *
     * @param LoadHandler $subject
     * @param CartInterface $quote
     * @return CartInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad($subject, $quote)
    {
        $giftcardQuoteItems = $this->giftcardQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quote->getId())
            ->addOrder('balance', Collection::SORT_ORDER_ASC)
            ->load()
            ->getItems();

        if (!$giftcardQuoteItems) {
            return $quote;
        }

        $extensionAttributes = $quote->getExtensionAttributes()
            ? $quote->getExtensionAttributes()
            : $this->cartExtensionFactory->create();
        $extensionAttributes->setAwGiftcardCodes($giftcardQuoteItems);
        $quote->setExtensionAttributes($extensionAttributes);

        return $quote;
    }
}
