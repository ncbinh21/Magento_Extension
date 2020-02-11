<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Service;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\GiftcardCartManagementInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote as QuoteModel;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterfaceFactory as GiftcardQuoteInterfaceFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote\CollectionFactory as GiftcardQuoteCollectionFactory;
use Aheadworks\Giftcard\Model\Giftcard\Validator as GiftcardValidator;

/**
 * Class GiftcardCartService
 *
 * @package Aheadworks\Giftcard\Model\Service
 */
class GiftcardCartService implements GiftcardCartManagementInterface
{
    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var GiftcardQuoteInterfaceFactory
     */
    private $giftcardQuoteFactory;

    /**
     * @var GiftcardQuoteCollectionFactory
     */
    private $giftcardQuoteCollectionFactory;

    /**
     * @var GiftcardValidator
     */
    private $giftcardValidator;

    /**
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param CartExtensionFactory $cartExtensionFactory
     * @param GiftcardQuoteInterfaceFactory $giftcardQuoteFactory
     * @param GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory
     * @param GiftcardValidator $giftcardValidator
     */
    public function __construct(
        GiftcardRepositoryInterface $giftcardRepository,
        CartRepositoryInterface $quoteRepository,
        CartExtensionFactory $cartExtensionFactory,
        GiftcardQuoteInterfaceFactory $giftcardQuoteFactory,
        GiftcardQuoteCollectionFactory $giftcardQuoteCollectionFactory,
        GiftcardValidator $giftcardValidator
    ) {
        $this->giftcardRepository = $giftcardRepository;
        $this->quoteRepository = $quoteRepository;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->giftcardQuoteFactory = $giftcardQuoteFactory;
        $this->giftcardQuoteCollectionFactory = $giftcardQuoteCollectionFactory;
        $this->giftcardValidator = $giftcardValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId, $activeQuote = true)
    {
        /** @var $quote QuoteModel */
        $quote = $activeQuote
            ? $this->quoteRepository->getActive($cartId)
            : $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $giftcards = [];
        if ($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwGiftcardCodes()) {
            $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
        }
        return $giftcards;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $giftcardCode, $activeQuote = true)
    {
        $giftcardCode = trim($giftcardCode);
        /** @var $quote QuoteModel */
        $quote = $activeQuote
            ? $this->quoteRepository->getActive($cartId)
            : $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $giftcard = $this->giftcardRepository->getByCode($giftcardCode, $quote->getStore()->getWebsiteId());
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('The specified Gift Card code is not valid'));
        }

        if (!$this->giftcardValidator->isValid($giftcard)) {
            $messages = $this->giftcardValidator->getMessages();
            throw new LocalizedException($messages[0]);
        }

        $giftcardQuoteItems = $this->giftcardQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quote->getId())
            ->addFieldToFilter('giftcard_id', $giftcard->getId())
            ->load()
            ->getItems();
        if ($giftcardQuoteItems) {
            throw new LocalizedException(__('The specified Gift Card code already in the quote'));
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $this->addGiftcardToQuote($giftcard, $quote);
            $this->quoteRepository->save($quote->collectTotals());

            if ($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwGiftcardCodes()) {
                $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
                /** @var GiftcardQuoteInterface $giftcard */
                foreach ($giftcards as $giftcard) {
                    if ($giftcard->getGiftcardCode() == $giftcardCode && $giftcard->isRemove()) {
                        throw new \Exception();
                    }
                }
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('The specified Gift Card code not be added'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $giftcardCode, $activeQuote = true)
    {
        $giftcardCode = trim($giftcardCode);
        /** @var $quote QuoteModel */
        $quote = $activeQuote
            ? $this->quoteRepository->getActive($cartId)
            : $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            if (!$this->removeGiftcardFromQuote($giftcardCode, $quote)) {
                throw new NoSuchEntityException(__('The specified Gift Card code is not valid'));
            }
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('The specified Gift Card code not be removed'));
        }
        return true;
    }

    /**
     * Add Gift Card code to quote
     *
     * @param GiftcardInterface $giftcard
     * @param QuoteModel $quote
     * @return void
     */
    private function addGiftcardToQuote($giftcard, $quote)
    {
        $extensionAttributes = $quote->getExtensionAttributes()
            ? $quote->getExtensionAttributes()
            : $this->cartExtensionFactory->create();

        /** @var GiftcardQuoteInterface $giftcardQuoteObject */
        $giftcardQuoteObject = $this->giftcardQuoteFactory->create();
        $giftcardQuoteObject
            ->setGiftcardId($giftcard->getId())
            ->setGiftcardCode($giftcard->getCode())
            ->setGiftcardBalance($giftcard->getBalance())
            ->setQuoteId($quote->getId())
            ->setBaseGiftcardAmount($giftcard->getBalance());

        $giftcards = [$giftcardQuoteObject];
        if ($extensionAttributes->getAwGiftcardCodes()) {
            $giftcards = array_merge($giftcards, $extensionAttributes->getAwGiftcardCodes());
        }
        $giftcards = $this->sortGiftcards($giftcards);
        $extensionAttributes->setAwGiftcardCodes($giftcards);

        $quote->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Sort Gift Card codes by asc
     *
     * @param GiftcardQuoteInterface[] $giftcards
     * @return GiftcardQuoteInterface[]
     */
    private function sortGiftcards($giftcards)
    {
        usort($giftcards, function (GiftcardQuoteInterface $a, GiftcardQuoteInterface $b) {
            if ($a->getGiftcardBalance() == $b->getGiftcardBalance()) {
                return 0;
            }
            return $a->getGiftcardBalance() > $b->getGiftcardBalance() ? 1 : -1;
        });
        return $giftcards;
    }

    /**
     * Remove Gift Card code from quote
     *
     * @param string $giftcardCode
     * @param QuoteModel $quote
     * @return bool
     */
    private function removeGiftcardFromQuote($giftcardCode, $quote)
    {
        if ($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwGiftcardCodes()) {
            $giftcards = $quote->getExtensionAttributes()->getAwGiftcardCodes();
            /** @var GiftcardQuoteInterface $giftcard */
            foreach ($giftcards as $giftcard) {
                if ($giftcard->getGiftcardCode() == $giftcardCode) {
                    $giftcard->setIsRemove(true);
                    return true;
                }
            }
        }
        return false;
    }
}
