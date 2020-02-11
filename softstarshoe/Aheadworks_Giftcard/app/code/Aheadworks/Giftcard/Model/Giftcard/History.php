<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard;

use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class HistoryAction
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class History extends AbstractExtensibleModel implements HistoryActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftcardId()
    {
        return $this->getData(self::GIFTCARD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftcardId($giftcardId)
    {
        return $this->setData(self::GIFTCARD_ID, $giftcardId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalanceAmount()
    {
        return $this->getData(self::BALANCE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalanceAmount($balanceAmount)
    {
        return $this->setData(self::BALANCE_AMOUNT, $balanceAmount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalanceDelta()
    {
        return $this->getData(self::BALANCE_DELTA);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalanceDelta($balanceDelta)
    {
        return $this->setData(self::BALANCE_DELTA, $balanceDelta);
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentPlaceholder()
    {
        return $this->getData(self::COMMENT_PLACEHOLDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentPlaceholder($commentPlaceholder)
    {
        return $this->setData(self::COMMENT_PLACEHOLDER, $commentPlaceholder);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionType()
    {
        return $this->getData(self::ACTION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setActionType($actionType)
    {
        return $this->setData(self::ACTION_TYPE, $actionType);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        return $this->getData(self::ENTITIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntities($entities)
    {
        return $this->setData(self::ENTITIES, $entities);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
