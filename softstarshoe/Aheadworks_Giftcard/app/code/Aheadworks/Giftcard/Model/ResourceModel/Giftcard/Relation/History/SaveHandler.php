<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Relation\History;

use Aheadworks\Giftcard\Model\Giftcard as GiftcardModel;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Aheadworks\Giftcard\Model\Source\History\Action as SourceHistoryAction;
use Aheadworks\Giftcard\Model\Giftcard\History\CommentPool;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Aheadworks\Giftcard\Model\Source\History\Comment\Action as SourceHistoryCommentAction;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardType;

/**
 * Class SaveHandler
 * @package Aheadworks\ShopByBrand\Model\Brand\Content
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AuthSession
     */
    private $adminSession;

    /**
     * @var CommentPool
     */
    private $commentPool;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var HistoryEntityInterfaceFactory
     */
    private $historyEntityFactory;

    /**
     * @var GiftcardType
     */
    private $sourceGiftcardType;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param AuthSession $adminSession
     * @param CommentPool $commentPool
     * @param EntityManager $entityManager
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param HistoryEntityInterfaceFactory $historyEntityFactory
     * @param GiftcardType $sourceGiftcardType
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        AuthSession $adminSession,
        CommentPool $commentPool,
        EntityManager $entityManager,
        HistoryActionInterfaceFactory $historyActionFactory,
        HistoryEntityInterfaceFactory $historyEntityFactory,
        GiftcardType $sourceGiftcardType
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->adminSession = $adminSession;
        $this->commentPool = $commentPool;
        $this->entityManager = $entityManager;
        $this->historyActionFactory = $historyActionFactory;
        $this->historyEntityFactory = $historyEntityFactory;
        $this->sourceGiftcardType = $sourceGiftcardType;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $this->registerAction($entity);
        return $entity;
    }

    /**
     * Register Gift Card action in history
     *
     * @param GiftcardModel $entity
     * @return $this
     */
    private function registerAction($entity)
    {
        $this->prepareCurrentHistoryAction($entity);
        /** @var HistoryActionInterface $historyAction */
        $historyAction = $entity->getCurrentHistoryAction();

        if (!$entity->getOrigData('id')) {
            $historyAction->setAction(SourceHistoryAction::CREATED);
        }
        if ($entity->getOrigData('id')) {
            if ($entity->getOrigData('balance') > $entity->getBalance()
                && $historyAction->getActionType() == SourceHistoryCommentAction::APPLIED_TO_ORDER
            ) {
                $action = $entity->getBalance() > 0
                    ? SourceHistoryAction::UPDATED
                    : SourceHistoryAction::USED;
                $historyAction->setAction($action);
            } elseif ($historyAction->getActionType() == SourceHistoryCommentAction::REFUND_GIFTCARD ||
                ($entity->getOrigData('state') != Status::DEACTIVATED && $entity->getState() == Status::DEACTIVATED)
            ) {
                $historyAction->setAction(SourceHistoryAction::DEACTIVATED);
            } elseif ($entity->getOrigData('state') == Status::DEACTIVATED
                && $entity->getState() != Status::DEACTIVATED
            ) {
                $historyAction->setAction(SourceHistoryAction::ACTIVATED);
            } elseif ($this->isEntityUpdated($entity, $historyAction)) {
                $historyAction->setAction(SourceHistoryAction::UPDATED);
            }
        }
        if ($entity->getOrigData('state') && $entity->getState() == Status::EXPIRED) {
            $historyAction->setAction(SourceHistoryAction::EXPIRED);
        }

        if (!$historyAction->getAction()) {
            return $this;
        }

        /** @var GiftcardModel\History\CommentInterface $commentRender */
        $commentRender = $this->commentPool->get($historyAction->getActionType());
        $balanceDelta = $entity->getBalance() - $entity->getOrigData('balance');
        $historyAction
            ->setGiftcardId($entity->getId())
            ->setBalanceDelta($balanceDelta)
            ->setBalanceAmount($entity->getBalance())
            ->setComment($commentRender->renderComment($historyAction->getEntities()))
            ->setCommentPlaceholder($commentRender->getLabel());

        $this->entityManager->save($historyAction);
        if ($historyAction->getEntities()) {
            foreach ($historyAction->getEntities() as $entity) {
                $entity->setHistoryId($historyAction->getId());
                $this->entityManager->save($entity);
            }
        }
        return $this;
    }

    /**
     * Check is entity updated
     *
     * @param GiftcardModel $entity
     * @param HistoryActionInterface $historyAction
     * @return bool
     */
    private function isEntityUpdated($entity, $historyAction)
    {
        return $entity->getOrigData('balance') != $entity->getBalance()
            || $this->isGiftcardTypeChanged($entity)
            || $historyAction->getActionType() != SourceHistoryCommentAction::BY_ADMIN;
    }

    /**
     * Prepare current history action
     *
     * @param GiftcardModel $entity
     * @return void
     */
    private function prepareCurrentHistoryAction($entity)
    {
        if (!$entity->getCurrentHistoryAction()) {
            /** @var HistoryEntityInterface $adminHistoryEntityObject */
            $adminHistoryEntityObject = $this->historyEntityFactory->create();
            $adminHistoryEntityObject
                ->setEntityType(SourceHistoryEntityType::ADMIN_ID)
                ->setEntityId($this->getAdminUserId())
                ->setEntityLabel($this->getAdminUserName());

            /** @var HistoryActionInterface $historyObject */
            $historyObject = $this->historyActionFactory->create();

            if ($this->isGiftcardTypeChanged($entity)) {
                /** @var HistoryEntityInterface $fromHistoryEntityObject */
                $fromHistoryEntityObject = $this->historyEntityFactory->create();
                $fromHistoryEntityObject
                    ->setEntityType(SourceHistoryEntityType::FROM)
                    ->setEntityId($entity->getOrigData('type'))
                    ->setEntityLabel($this->sourceGiftcardType->getOptionText($entity->getOrigData('type')));
                /** @var HistoryEntityInterface $toHistoryEntityObject */
                $toHistoryEntityObject = $this->historyEntityFactory->create();
                $toHistoryEntityObject
                    ->setEntityType(SourceHistoryEntityType::TO)
                    ->setEntityId($entity->getType())
                    ->setEntityLabel($this->sourceGiftcardType->getOptionText($entity->getType()));

                $historyObject
                    ->setActionType(SourceHistoryCommentAction::TYPE_CHANGED)
                    ->setEntities([$fromHistoryEntityObject, $toHistoryEntityObject, $adminHistoryEntityObject]);
            } else {
                $historyObject
                    ->setActionType(SourceHistoryCommentAction::BY_ADMIN)
                    ->setEntities([$adminHistoryEntityObject]);
            }
            $entity->setCurrentHistoryAction($historyObject);
        }
    }

    /**
     * Check is Gift Card type changed
     *
     * @param GiftcardModel $entity
     * @return bool
     */
    private function isGiftcardTypeChanged($entity)
    {
        return $entity->getOrigData('id') && $entity->getOrigData('type') != $entity->getType();
    }

    /**
     * Get current admin user id
     *
     * @return int|null
     */
    private function getAdminUserId()
    {
        $userId = 0;
        if ($this->adminSession->getUser()) {
            $userId = $this->adminSession->getUser()->getId();
        }
        return $userId;
    }

    /**
     * Get current admin user name
     *
     * @return string|null
     */
    private function getAdminUserName()
    {
        $userName = 'Unknown';
        if ($this->adminSession->getUser()) {
            $userName = $this->adminSession->getUser()->getFirstName()
                . ' ' . $this->adminSession->getUser()->getLastName();
        }
        return $userName;
    }
}
