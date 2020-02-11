<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Service;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\GiftcardManagementInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Giftcard\CodeGenerator;
use Aheadworks\Giftcard\Model\Source\Giftcard\EmailTemplate;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Giftcard\Model\Giftcard\Validator as GiftcardValidator;
use Aheadworks\Giftcard\Model\Email\Sender;
use Aheadworks\Giftcard\Model\Giftcard\Grouping as GiftcardGrouping;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Aheadworks\Giftcard\Model\Source\History\Comment\Action as SourceHistoryCommentAction;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Giftcard\Model\Import\GiftcardCode as ImportGiftcardCode;

/**
 * Class GiftcardService
 *
 * @package Aheadworks\Giftcard\Model\Service
 */
class GiftcardService implements GiftcardManagementInterface
{
    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var GiftcardValidator
     */
    private $giftcardValidator;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var GiftcardGrouping
     */
    private $giftcardGrouping;

    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var HistoryEntityInterfaceFactory
     */
    private $historyEntityFactory;

    /**
     * @var EmailStatus
     */
    private $sourceEmailStatus;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * OrderStatusHistoryInterfaceFactory
     */
    private $orderStatusHistoryFactory;

    /**
     * OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * CodeGenerator
     */
    private $codeGenerator;

    /**
     * ImportGiftcardCode
     */
    private $importGiftcardCode;

    /**
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PriceCurrencyInterface $priceCurrency
     * @param CustomerSession $customerSession
     * @param GiftcardValidator $giftcardValidator
     * @param Sender $sender
     * @param GiftcardGrouping $giftcardGrouping
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param HistoryEntityInterfaceFactory $historyEntityFactory
     * @param EmailStatus $sourceEmailStatus
     * @param OrderManagementInterface $orderManagement
     * @param OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param CodeGenerator $codeGenerator
     * @param ImportGiftcardCode $importGiftcardCode
     */
    public function __construct(
        GiftcardRepositoryInterface $giftcardRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PriceCurrencyInterface $priceCurrency,
        CustomerSession $customerSession,
        GiftcardValidator $giftcardValidator,
        Sender $sender,
        GiftcardGrouping $giftcardGrouping,
        HistoryActionInterfaceFactory $historyActionFactory,
        HistoryEntityInterfaceFactory $historyEntityFactory,
        EmailStatus $sourceEmailStatus,
        OrderManagementInterface $orderManagement,
        OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory,
        OrderRepositoryInterface $orderRepository,
        CodeGenerator $codeGenerator,
        ImportGiftcardCode $importGiftcardCode
    ) {
        $this->giftcardRepository = $giftcardRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->giftcardValidator = $giftcardValidator;
        $this->sender = $sender;
        $this->giftcardGrouping = $giftcardGrouping;
        $this->historyActionFactory = $historyActionFactory;
        $this->historyEntityFactory = $historyEntityFactory;
        $this->sourceEmailStatus = $sourceEmailStatus;
        $this->orderManagement = $orderManagement;
        $this->orderStatusHistoryFactory = $orderStatusHistoryFactory;
        $this->orderRepository = $orderRepository;
        $this->codeGenerator = $codeGenerator;
        $this->importGiftcardCode = $importGiftcardCode;
    }

    /**
     * {@inheritdoc}
     */
    public function sendGiftcardByCode($giftcardCodes, $validateDeliveryDate = true, $storeId = null)
    {
        if (!is_array($giftcardCodes)) {
            $giftcardCodes = [$giftcardCodes];
        }

        $validGiftcards = [];
        foreach ($giftcardCodes as $giftcardCode) {
            try {
                $giftcard = $this->giftcardRepository->getByCode($giftcardCode);
                if ($giftcard->getEmailTemplate() == EmailTemplate::DO_NOT_SEND
                    || !$this->giftcardValidator->isValid($giftcard)
                    || ($validateDeliveryDate && !$this->isSendToday($giftcard->getDeliveryDate()))
                ) {
                    continue;
                }
                $validGiftcards[] = $giftcard;
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $giftcardsToResult = [];
        $giftcardsGrouped = $this->giftcardGrouping->process($validGiftcards);
        foreach ($giftcardsGrouped as $giftcardsGroup) {
            $sendStatus = $this->sender->sendGiftcards($giftcardsGroup);
            $sendStatusText = $this->sourceEmailStatus->getOptionByValue($sendStatus);
            /** @var HistoryEntityInterface $creditmemoHistoryEntityObject */
            $statusHistoryEntityObject = $this->historyEntityFactory->create();
            $statusHistoryEntityObject
                ->setEntityType(SourceHistoryEntityType::EMAIL_STATUS)
                ->setEntityId($sendStatus)
                ->setEntityLabel($sendStatusText);

            /** @var HistoryActionInterface $historyObject */
            $historyObject = $this->historyActionFactory->create();
            $historyObject
                ->setActionType(SourceHistoryCommentAction::DELIVERY_DATE_EMAIL_STATUS)
                ->setEntities([$statusHistoryEntityObject]);
            /** @var GiftcardInterface $giftcard */
            foreach ($giftcardsGroup as $giftcard) {
                $giftcard
                    ->setCurrentHistoryAction($historyObject)
                    ->setEmailSent($sendStatus);
                $this->giftcardRepository->save($giftcard);
                if ($giftcard->getOrderId()) {
                    $comment = __('Gift Cart code %1 email delivery status: %2', $giftcard->getCode(), $sendStatusText);
                    $this->addCommentToGiftcardOrder($giftcard, $comment);
                }
                $giftcardsToResult[] = $giftcard;
            }
        }

        return $giftcardsToResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGiftcards($customerEmail = null, $cartId = null, $storeId = null)
    {
        $giftcards = [];
        if (!$customerEmail) {
            $customerEmail = $this->customerSession->getCustomer()->getEmail();
        }
        if (!$customerEmail) {
            return $giftcards;
        }
        if ($cartId) {
            $this->searchCriteriaBuilder->addFilter('quote', $cartId);
        }
        $this->searchCriteriaBuilder
            ->addFilter(GiftcardInterface::RECIPIENT_EMAIL, $customerEmail)
            ->addFilter(GiftcardInterface::STATE, Status::ACTIVE)
            ->addFilter(GiftcardInterface::EMAIL_SENT, [EmailStatus::SENT, EmailStatus::NOT_SEND], 'in');
        $giftcards = $this->giftcardRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        /** @var GiftcardInterface $giftcardCode */
        foreach ($giftcards as $giftcard) {
            $giftcard->setBalance($this->priceCurrency->convert($giftcard->getBalance(), $storeId));
        }
        return $giftcards;
    }

    /**
     * {@inheritdoc}
     */
    public function addCommentToGiftcardOrder($giftcard, $comment, $visibleOnFront = 0, $customerNotified = 0)
    {
        if ($orderId = $giftcard->getOrderId()) {
            try {
                $order = $this->orderRepository->get($orderId);
                /** @var OrderStatusHistoryInterface $orderStatusHistoryObject */
                $orderStatusHistoryObject = $this->orderStatusHistoryFactory->create();
                $orderStatusHistoryObject
                    ->setComment($comment)
                    ->setIsVisibleOnFront($visibleOnFront)
                    ->setIsCustomerNotified($customerNotified)
                    ->setStatus($order->getStatus())
                    ->setEntityName('order');

                return $this->orderManagement->addComment(
                    $orderId,
                    $orderStatusHistoryObject
                );
            } catch (NoSuchEntityException $e) {
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCodes($websiteId = null, $codeGenerationSettings = null)
    {
        return $this->codeGenerator->generate($codeGenerationSettings, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function importCodes($codesRawData)
    {
        $giftcardCodes = [];
        if (!$codesRawData) {
            return $giftcardCodes;
        }
        $giftcardCodes = $this->importGiftcardCode->process($codesRawData);
        foreach ($giftcardCodes as $giftcardCode) {
            /** @var GiftcardInterface $giftcardCode */
            $this->giftcardRepository->save($giftcardCode);
        }

        return $giftcardCodes;
    }

    /**
     * Check is send today
     *
     * @param string $deliveryDate
     * @return bool
     */
    private function isSendToday($deliveryDate)
    {
        if (!$deliveryDate) {
            return true;
        }
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $deliveryDate = new \DateTime($deliveryDate);

        if ($deliveryDate <= $now) {
            return true;
        }
        return false;
    }
}
