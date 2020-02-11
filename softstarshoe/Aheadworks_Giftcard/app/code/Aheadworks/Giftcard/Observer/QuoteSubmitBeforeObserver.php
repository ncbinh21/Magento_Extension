<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterfaceFactory as GiftcardOrderInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface as GiftcardOrderInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class QuoteSubmitBeforeObserver
 *
 * @package Aheadworks\Giftcard\Observer
 */
class QuoteSubmitBeforeObserver implements ObserverInterface
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var GiftcardOrderInterfaceFactory
     */
    private $giftcardOrderFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param GiftcardOrderInterfaceFactory $giftcardOrderFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        GiftcardOrderInterfaceFactory $giftcardOrderFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->giftcardOrderFactory = $giftcardOrderFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getBaseAwGiftcardAmount() && $quote->getBaseAwGiftcardAmount() > 0) {
            $order->setBaseAwGiftcardAmount($quote->getBaseAwGiftcardAmount());
            $order->setAwGiftcardAmount($quote->getAwGiftcardAmount());

            $extensionAttributes = $order->getExtensionAttributes()
                ? $order->getExtensionAttributes()
                : $this->orderExtensionFactory->create();

            $quoteGiftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
            $orderGiftcards = [];
            /** @var GiftcardQuoteInterface $quoteGiftcard */
            foreach ($quoteGiftcards as $quoteGiftcard) {
                /** @var GiftcardOrderInterface $orderGiftcard */
                $orderGiftcard = $this->giftcardOrderFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $orderGiftcard,
                    $quoteGiftcard->getData(),
                    GiftcardOrderInterface::class
                );
                $orderGiftcard->setId(null);
                $orderGiftcards[] = $orderGiftcard;
            }
            $extensionAttributes->setAwGiftcardCodes($orderGiftcards);
            $order->setExtensionAttributes($extensionAttributes);
        }
    }
}
