<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Option
 *
 * @package Aheadworks\Giftcard\Model\Product
 */
class Option extends AbstractExtensibleModel implements OptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAwGcAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcCustomAmount()
    {
        return $this->getData(self::CUSTOM_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcCustomAmount($amount)
    {
        return $this->setData(self::CUSTOM_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcTemplate()
    {
        return $this->getData(self::TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcTemplate($template)
    {
        return $this->setData(self::TEMPLATE, $template);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcTemplateName()
    {
        return $this->getData(self::TEMPLATE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcTemplateName($templateName)
    {
        return $this->setData(self::TEMPLATE_NAME, $templateName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcSenderName()
    {
        return $this->getData(self::SENDER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcSenderName($senderName)
    {
        return $this->setData(self::SENDER_NAME, $senderName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcSenderEmail()
    {
        return $this->getData(self::SENDER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcSenderEmail($senderEmail)
    {
        return $this->setData(self::SENDER_EMAIL, $senderEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcRecipientName($recipientName)
    {
        return $this->setData(self::RECIPIENT_NAME, $recipientName);
    }

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getAwGcRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcHeadline()
    {
        return $this->getData(self::HEADLINE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcHeadline($headline)
    {
        return $this->setData(self::HEADLINE, $headline);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcType()
    {
        return $this->getData(self::GIFTCARD_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcType($giftcardType)
    {
        return $this->setData(self::GIFTCARD_TYPE, $giftcardType);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcDeliveryDate()
    {
        return $this->getData(self::DELIVERY_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcDeliveryDate($deliveryDate)
    {
        return $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcDeliveryDateTimezone()
    {
        return $this->getData(self::DELIVERY_DATE_TIMEZONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcDeliveryDateTimezone($deliveryDateTimezone)
    {
        return $this->setData(self::DELIVERY_DATE_TIMEZONE, $deliveryDateTimezone);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwGcCreatedCodes()
    {
        return $this->getData(self::GIFTCARD_CODES);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwGcCreatedCodes($giftcardCodes)
    {
        return $this->setData(self::GIFTCARD_CODES, $giftcardCodes);
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
        \Aheadworks\Giftcard\Api\Data\OptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
