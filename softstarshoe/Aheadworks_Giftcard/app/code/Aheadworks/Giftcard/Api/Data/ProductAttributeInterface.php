<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Catalog\Api\Data\ProductAttributeInterface as CatalogProductAttributeInterface;

/**
 * Interface ProductAttributeInterface
 * @api
 */
interface ProductAttributeInterface extends CatalogProductAttributeInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const CODE_AW_GC_TYPE = 'aw_gc_type';
    const CODE_AW_GC_POOL = 'aw_gc_pool';
    const CODE_AW_GC_DESCRIPTION = 'aw_gc_description';
    const CODE_AW_GC_EXPIRE = 'aw_gc_expire';
    const CODE_AW_GC_CUSTOM_MESSAGE_FIELDS = 'aw_gc_custom_message_fields';
    const CODE_AW_GC_EMAIL_TEMPLATES = 'aw_gc_email_templates';
    const CODE_AW_GC_AMOUNTS = 'aw_gc_amounts';
    const CODE_AW_GC_ALLOW_OPEN_AMOUNT = 'aw_gc_allow_open_amount';
    const CODE_AW_GC_OPEN_AMOUNT_MIN = 'aw_gc_open_amount_min';
    const CODE_AW_GC_OPEN_AMOUNT_MAX = 'aw_gc_open_amount_max';
    const CODE_AW_GC_ALLOW_DELIVERY_DATE = 'aw_gc_allow_delivery_date';
    const CODE_AW_GC_DAYS_ORDER_DELIVERY = 'aw_gc_days_order_delivery';
    /**#@-*/

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
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setCode($description);

    /**
     * Get expire
     *
     * @return int
     */
    public function getExpire();

    /**
     * Set expire
     *
     * @param int $expire
     * @return $this
     */
    public function setExpire($expire);

    /**
     * Is allow message
     *
     * @return bool
     */
    public function isAllowMessage();

    /**
     * Set allow message
     *
     * @param bool $allowMessage
     * @return $this
     */
    public function setAllowMessage($allowMessage);

    /**
     * Get email templates
     *
     * @return \Aheadworks\Giftcard\Api\Data\TemplateInterface
     */
    public function getEmailTemplates();

    /**
     * Set email templates
     *
     * @param \Aheadworks\Giftcard\Api\Data\TemplateInterface $emailTemplates
     * @return $this
     */
    public function setEmailTemplates($emailTemplates);

    /**
     * Get amounts
     *
     * @return \Aheadworks\Giftcard\Api\Data\AmountInterface
     */
    public function getAmounts();

    /**
     * Set amounts
     *
     * @param \Aheadworks\Giftcard\Api\Data\AmountInterface $amounts
     * @return $this
     */
    public function setAmounts($amounts);

    /**
     * Get allow open amount
     *
     * @return bool
     */
    public function isAllowOpenAmount();

    /**
     * Set allow open amount
     *
     * @param bool $allowOpenAmount
     * @return $this
     */
    public function setAllowOpenAmount($allowOpenAmount);

    /**
     * Get open amount min
     *
     * @return float
     */
    public function getOpenAmountMin();

    /**
     * Set open amount min
     *
     * @param float $openAmountMin
     * @return $this
     */
    public function setOpenAmountMin($openAmountMin);

    /**
     * Get open amount max
     *
     * @return float
     */
    public function getOpenAmountMax();

    /**
     * Set open amount max
     *
     * @param float $openAmountMax
     * @return $this
     */
    public function setOpenAmountMax($openAmountMax);

    /**
     * Get allow delivery date
     *
     * @return bool
     */
    public function getAllowDeliveryDate();

    /**
     * Set allow delivery date
     *
     * @param bool $allowDeliveryDate
     * @return $this
     */
    public function setAllowDeliveryDate($allowDeliveryDate);

    /**
     * Get days between order and delivery dates
     *
     * @return int
     */
    public function getDaysOrderDelivery();

    /**
     * Set days between order and delivery dates
     *
     * @param int $daysOrderDelivery
     * @return $this
     */
    public function setDaysOrderDelivery($daysOrderDelivery);
}
