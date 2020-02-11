<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Entity\Attribute;

use Aheadworks\Giftcard\Api\Data\TemplateInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Template
 *
 * @package Aheadworks\Giftcard\Model\Product\Entity\Attribute
 */
class Template extends AbstractExtensibleObject implements TemplateInterface
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
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
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
        \Aheadworks\Giftcard\Api\Data\TemplateExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
