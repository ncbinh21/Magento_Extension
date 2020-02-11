<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model\Quote\QuoteRepository;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository\SaveHandler;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class SaveHandlerPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model\Quote\QuoteRepository
 */
class SaveHandlerPlugin
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * Save Gift Card codes to Gift Card quote table
     *
     * @param SaveHandler $subject
     * @param CartInterface $quote
     * @return CartInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave($subject, $quote)
    {
        return $this->saveGiftcardToQuote($quote);
    }

    /**
     * Save Gift Card codes to Gift Card quote table
     *
     * @param Quote $quote
     * @return Quote
     */
    public function saveGiftcardToQuote($quote)
    {
        if ($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwGiftcardCodes()) {
            $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
            /** @var GiftcardQuoteInterface $giftcard */
            foreach ($giftcards as $giftcard) {
                if ($giftcard->isRemove()) {
                    $this->entityManager->delete($giftcard);
                } else {
                    $this->entityManager->save($giftcard);
                }
            }
        }

        return $quote;
    }
}
