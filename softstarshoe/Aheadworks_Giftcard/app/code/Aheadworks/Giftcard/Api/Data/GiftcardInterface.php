<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface GiftcardInterface
 * @api
 */
interface GiftcardInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const CODE = 'code';
    const TYPE = 'type';
    const CREATED_AT = 'created_at';
    const EXPIRE_AT = 'expire_at';
    const WEBSITE_ID = 'website_id';
    const BALANCE = 'balance';
    const INITIAL_BALANCE = 'initial_balance';
    const STATE = 'state';
    const ORDER_ID = 'order_id';
    const PRODUCT_ID = 'product_id';
    const EMAIL_TEMPLATE = 'email_template';
    const SENDER_NAME = 'sender_name';
    const SENDER_EMAIL = 'sender_email';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_EMAIL = 'recipient_email';
    const DELIVERY_DATE = 'delivery_date';
    const DELIVERY_DATE_TIMEZONE = 'delivery_date_timezone';
    const EMAIL_SENT = 'email_sent';
    const HEADLINE = 'headline';
    const MESSAGE = 'message';
    const CURRENT_HISTORY_ACTION = 'current_history_action';
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
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get type
     *
     * @return int
     */
    public function getType();

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get expire at
     *
     * @return string
     */
    public function getExpireAt();

    /**
     * Set expire at
     *
     * @param string $expireAt
     * @return $this
     */
    public function setExpireAt($expireAt);

    /**
     * Get website id
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * Get balance
     *
     * @return float
     */
    public function getBalance();

    /**
     * Set balance
     *
     * @param float $balance
     * @return $this
     */
    public function setBalance($balance);

    /**
     * Get initial balance
     *
     * @return float
     */
    public function getInitialBalance();

    /**
     * Set initial balance
     *
     * @param float $initialBalance
     * @return $this
     */
    public function setInitialBalance($initialBalance);

    /**
     * Get state
     *
     * @return int
     */
    public function getState();

    /**
     * Set state
     *
     * @param int $state
     * @return $this
     */
    public function setState($state);

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
     * Get product id
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailTemplate();

    /**
     * Set email template
     *
     * @param string $emailTemplate
     * @return $this
     */
    public function setEmailTemplate($emailTemplate);

    /**
     * Get sender name
     *
     * @return string
     */
    public function getSenderName();

    /**
     * Set sender name
     *
     * @param string $senderName
     * @return $this
     */
    public function setSenderName($senderName);

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail();

    /**
     * Set sender email
     *
     * @param string $senderEmail
     * @return $this
     */
    public function setSenderEmail($senderEmail);

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Set recipient name
     *
     * @param string $recipientName
     * @return $this
     */
    public function setRecipientName($recipientName);

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * Get delivery date
     *
     * @return string
     */
    public function getDeliveryDate();

    /**
     * Set delivery date
     *
     * @param string $deliveryDate
     * @return $this
     */
    public function setDeliveryDate($deliveryDate);

    /**
     * Get delivery date timezone
     *
     * @return string
     */
    public function getDeliveryDateTimezone();

    /**
     * Set delivery date timezone
     *
     * @param string $deliveryDateTimezone
     * @return $this
     */
    public function setDeliveryDateTimezone($deliveryDateTimezone);

    /**
     * Get email sent
     *
     * @return int
     */
    public function getEmailSent();

    /**
     * Set email sent
     *
     * @param int $emailSent
     * @return $this
     */
    public function setEmailSent($emailSent);

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline();

    /**
     * Set headline
     *
     * @param string $headline
     * @return $this
     */
    public function setHeadline($headline);

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get current history action
     *
     * @return \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface
     */
    public function getCurrentHistoryAction();

    /**
     * Set current history action
     *
     * @param \Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface $value
     * @return $this
     */
    public function setCurrentHistoryAction($value);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\GiftcardExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\GiftcardExtensionInterface $extensionAttributes
    );
}
