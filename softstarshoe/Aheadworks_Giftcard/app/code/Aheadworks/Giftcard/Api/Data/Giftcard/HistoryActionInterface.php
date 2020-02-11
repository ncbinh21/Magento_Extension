<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data\Giftcard;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface HistoryActionInterface
 * @api
 */
interface HistoryActionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const GIFTCARD_ID = 'giftcard_id';
    const UPDATED_AT = 'updated_at';
    const ACTION = 'action';
    const BALANCE_AMOUNT = 'balance_amount';
    const BALANCE_DELTA = 'balance_delta';
    const COMMENT = 'comment';
    const COMMENT_PLACEHOLDER = 'comment_placeholder';
    const ACTION_TYPE = 'action_type';
    const ENTITIES = 'entities';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get giftcard Id
     *
     * @return string
     */
    public function getGiftcardId();

    /**
     * Set giftcard Id
     *
     * @param int $giftcardId
     * @return $this
     */
    public function setGiftcardId($giftcardId);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get action
     *
     * @return int
     */
    public function getAction();

    /**
     * Set action
     *
     * @param int $action
     * @return $this
     */
    public function setAction($action);

    /**
     * Get balance amount
     *
     * @return float
     */
    public function getBalanceAmount();

    /**
     * Set balance amount
     *
     * @param float $balanceAmount
     * @return $this
     */
    public function setBalanceAmount($balanceAmount);

    /**
     * Get balance delta
     *
     * @return float
     */
    public function getBalanceDelta();

    /**
     * Set balance delta
     *
     * @param float $balanceDelta
     * @return $this
     */
    public function setBalanceDelta($balanceDelta);

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment);

    /**
     * Get comment placeholder
     *
     * @return string
     */
    public function getCommentPlaceholder();

    /**
     * Set comment placeholder
     *
     * @param string $commentPlaceholder
     * @return $this
     */
    public function setCommentPlaceholder($commentPlaceholder);

    /**
     * Get action type
     *
     * @return int
     */
    public function getActionType();

    /**
     * Set action type
     *
     * @param int $actionType
     * @return $this
     */
    public function setActionType($actionType);

    /**
     * Get entities
     *
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface[]
     */
    public function getEntities();

    /**
     * Set entities
     *
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface[] $entities
     * @return $this
     */
    public function setEntities($entities);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionExtensionInterface $extensionAttributes
    );
}
