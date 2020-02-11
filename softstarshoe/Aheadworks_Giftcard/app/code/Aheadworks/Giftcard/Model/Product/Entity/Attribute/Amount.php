<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Entity\Attribute;

use Aheadworks\Giftcard\Api\Data\AmountInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Amount
 *
 * @package Aheadworks\Giftcard\Model\Product\Entity\Attribute
 */
class Amount extends AbstractExtensibleObject implements AmountInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValueId()
    {
        return $this->_get(self::VALUE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueId($valueId)
    {
        return $this->setData(self::VALUE_ID, $valueId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
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
        \Aheadworks\Giftcard\Api\Data\AmountExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
