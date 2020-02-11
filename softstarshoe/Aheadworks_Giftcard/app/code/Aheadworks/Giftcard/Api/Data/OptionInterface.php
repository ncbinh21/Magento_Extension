<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface OptionInterface
 * @api
 */
interface OptionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const DELIVERY_DATE_FORMAT_ON_STOREFRONT = 'MM/dd/yyyy';
    const AMOUNT = 'aw_gc_amount';
    const CUSTOM_AMOUNT = 'aw_gc_custom_amount';
    const TEMPLATE = 'aw_gc_template';
    const TEMPLATE_NAME = 'aw_gc_template_name';
    const RECIPIENT_NAME = 'aw_gc_recipient_name';
    const RECIPIENT_EMAIL = 'aw_gc_recipient_email';
    const SENDER_NAME = 'aw_gc_sender_name';
    const SENDER_EMAIL = 'aw_gc_sender_email';
    const HEADLINE = 'aw_gc_headline';
    const MESSAGE = 'aw_gc_message';
    const GIFTCARD_TYPE = 'aw_gc_type';
    const DELIVERY_DATE = 'aw_gc_delivery_date';
    const DELIVERY_DATE_TIMEZONE = 'aw_gc_delivery_date_timezone';
    const GIFTCARD_CODES = 'aw_gc_created_codes';
    /**#@-*/

    /**
     * Get amount
     *
     * @return float|null
     */
    public function getAwGcAmount();

    /**
     * Set amount
     *
     * @param float|null $amount
     * @return $this
     */
    public function setAwGcAmount($amount);

    /**
     * Get custom amount
     *
     * @return float|null
     */
    public function getAwGcCustomAmount();

    /**
     * Set custom amount
     *
     * @param float|null $amount
     * @return $this
     */
    public function setAwGcCustomAmount($amount);

    /**
     * Get template
     *
     * @return string
     */
    public function getAwGcTemplate();

    /**
     * Set template
     *
     * @param string $template
     * @return $this
     */
    public function setAwGcTemplate($template);

    /**
     * Get template name
     *
     * @return int
     */
    public function getAwGcTemplateName();

    /**
     * Set template name
     *
     * @param int $templateName
     * @return $this
     */
    public function setAwGcTemplateName($templateName);

    /**
     * Get sender name
     *
     * @return string
     */
    public function getAwGcSenderName();

    /**
     * Set sender name
     *
     * @param string $senderName
     * @return $this
     */
    public function setAwGcSenderName($senderName);

    /**
     * Get sender email
     *
     * @return string
     */
    public function getAwGcSenderEmail();

    /**
     * Set sender email
     *
     * @param string $senderEmail
     * @return $this
     */
    public function setAwGcSenderEmail($senderEmail);

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getAwGcRecipientName();

    /**
     * Set recipient name
     *
     * @param string $recipientName
     * @return $this
     */
    public function setAwGcRecipientName($recipientName);

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getAwGcRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string $recipientEmail
     * @return $this
     */
    public function setAwGcRecipientEmail($recipientEmail);

    /**
     * Get headline
     *
     * @return string|null
     */
    public function getAwGcHeadline();

    /**
     * Set headline
     *
     * @param string|null $headline
     * @return $this
     */
    public function setAwGcHeadline($headline);

    /**
     * Get message
     *
     * @return string|null
     */
    public function getAwGcMessage();

    /**
     * Set message
     *
     * @param string|null $message
     * @return $this
     */
    public function setAwGcMessage($message);

    /**
     * Get gift card type
     *
     * @return int
     */
    public function getAwGcType();

    /**
     * Set gift card type
     *
     * @param int $giftcardType
     * @return $this
     */
    public function setAwGcType($giftcardType);

    /**
     * Get gift card delivery date
     *
     * @return string
     */
    public function getAwGcDeliveryDate();

    /**
     * Set gift card delivery date
     *
     * @param string $deliveryDate
     * @return $this
     */
    public function setAwGcDeliveryDate($deliveryDate);

    /**
     * Get gift card delivery date timezone
     *
     * @return string
     */
    public function getAwGcDeliveryDateTimezone();

    /**
     * Set gift card delivery date timezone
     *
     * @param string $deliveryDateTimezone
     * @return $this
     */
    public function setAwGcDeliveryDateTimezone($deliveryDateTimezone);

    /**
     * Get gift card codes
     *
     * @return string[]
     */
    public function getAwGcCreatedCodes();

    /**
     * Set gift card codes
     *
     * @param string[] $giftcardCodes
     * @return $this
     */
    public function setAwGcCreatedCodes($giftcardCodes);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Giftcard\Api\Data\OptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Giftcard\Api\Data\OptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\OptionExtensionInterface $extensionAttributes
    );
}
