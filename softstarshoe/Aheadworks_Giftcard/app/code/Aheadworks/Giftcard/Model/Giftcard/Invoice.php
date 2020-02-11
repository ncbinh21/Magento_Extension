<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard;

use Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Invoice
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class Invoice extends AbstractExtensibleModel implements InvoiceInterface
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
    public function getGiftcardCode()
    {
        return $this->getData(self::GIFTCARD_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftcardCode($giftcardCode)
    {
        return $this->setData(self::GIFTCARD_CODE, $giftcardCode);
    }

    /**
     * {@inheritdoc}
     */

    public function getInvoiceId()
    {
        return $this->getData(self::INVOICE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceId($invoiceId)
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseGiftcardAmount()
    {
        return $this->getData(self::BASE_GIFTCARD_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseGiftcardAmount($amount)
    {
        return $this->setData(self::BASE_GIFTCARD_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftcardAmount()
    {
        return $this->getData(self::GIFTCARD_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftcardAmount($amount)
    {
        return $this->setData(self::GIFTCARD_AMOUNT, $amount);
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
        \Aheadworks\Giftcard\Api\Data\Giftcard\InvoiceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
