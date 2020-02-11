<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote\CollectionFactory as GiftcardQuoteCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Plugin\Model\Quote\QuoteRepository\SaveHandlerPlugin;

/**
 * Class QuotePlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model
 */
class QuotePlugin
{
    /**
     * @var GiftcardQuoteCollectionFactory
     */
    private $giftcardQuoteCollectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SaveHandlerPlugin
     */
    private $saveHandlerPlugin;

    /**
     * @param GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory
     * @param EntityManager $entityManager
     * @param SaveHandlerPlugin $saveHandlerPlugin
     */
    public function __construct(
        GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory,
        EntityManager $entityManager,
        SaveHandlerPlugin $saveHandlerPlugin
    ) {
        $this->giftcardQuoteCollectionFactory = $giftcardQuoteCollectionFactory;
        $this->entityManager = $entityManager;
        $this->saveHandlerPlugin = $saveHandlerPlugin;
    }

    /**
     * Replace quote_id in Gift Card quote table after merge quote
     *
     * @param Quote $subject
     * @param \Closure $proceed
     * @param Quote $quote
     * @return Quote
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundMerge(Quote $subject, \Closure $proceed, Quote $quote)
    {
        /** @var Quote $quoteAfterMerge */
        $quoteAfterMerge = $proceed($quote);

        $giftcardQuoteItems = $this->giftcardQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quote->getId())
            ->load()
            ->getItems();

        if (!$giftcardQuoteItems) {
            return $quoteAfterMerge;
        }

        /** @var GiftcardQuoteInterface $giftcardQuoteItem */
        foreach ($giftcardQuoteItems as $giftcardQuoteItem) {
            $giftcardQuoteItem->setQuoteId($quoteAfterMerge->getId());
            $this->entityManager->save($giftcardQuoteItem);
        }

        return $quoteAfterMerge;
    }

    /**
     * Save Gift Card codes to Gift Card quote table (Compatible with EE Customer Balance)
     *
     * @param Quote $subject
     * @param Quote $quote
     * @return Quote
     */
    public function afterSave($subject, $quote)
    {
        return $this->saveHandlerPlugin->saveGiftcardToQuote($quote);
    }
}
