<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data\Giftcard;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CreditmemoInterface
 * @api
 */
interface CreditmemoInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const GIFTCARD_ID = 'giftcard_id';
    const GIFTCARD_CODE = 'giftcard_code';
    const CREDITMEMO_ID = 'creditmemo_id';
    const ORDER_ID = 'order_id';
    const BASE_GIFTCARD_AMOUNT = 'base_giftcard_amount';
    const GIFTCARD_AMOUNT = 'giftcard_amount';
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
     * Get Gift Card id
     *
     * @return int
     */
    public function getGiftcardId();

    /**
     * Set Gift Card id
     *
     * @param int $giftcardId
     * @return $this
     */
    public function setGiftcardId($giftcardId);

    /**
     * Set Gift Card code
     *
     * @param string $giftcardCode
     * @return $this
     */
    public function setGiftcardCode($giftcardCode);

    /**
     * Get Gift Card code
     *
     * @return string
     */
    public function getGiftcardCode();

    /**
     * Get credit memo id
     *
     * @return int
     */
    public function getCreditmemoId();

    /**
     * Set credit memo id
     *
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoId($creditmemoId);

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get base Gift Card amount
     *
     * @return float
     */
    public function getBaseGiftcardAmount();

    /**
     * Set base Gift Card amount
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseGiftcardAmount($amount);

    /**
     * Get Gift Card amount
     *
     * @return float
     */
    public function getGiftcardAmount();

    /**
     * Set Gift Card amount
     *
     * @param float $amount
     * @return $this
     */
    public function setGiftcardAmount($amount);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoExtensionInterface $extensionAttributes
    );
}
