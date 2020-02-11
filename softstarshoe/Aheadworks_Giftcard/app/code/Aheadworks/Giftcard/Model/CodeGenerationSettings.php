<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class CodeGenerationSettings
 *
 * @package Aheadworks\Giftcard\Model\Giftcard
 */
class CodeGenerationSettings extends AbstractExtensibleModel implements CodeGenerationSettingsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->getData(self::LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setLength($length)
    {
        return $this->setData(self::LENGTH, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return $this->getData(self::FORMAT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormat($format)
    {
        return $this->setData(self::FORMAT, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return $this->getData(self::PREFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrefix($prefix)
    {
        return $this->setData(self::PREFIX, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSuffix()
    {
        return $this->getData(self::SUFFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setSuffix($suffix)
    {
        return $this->setData(self::SUFFIX, $suffix);
    }

    /**
     * {@inheritdoc}
     */
    public function getDelimiterAtEvery()
    {
        return $this->getData(self::DELIMITER_AT_EVERY);
    }

    /**
     * {@inheritdoc}
     */
    public function setDelimiterAtEvery($delimiterAtEvery)
    {
        return $this->setData(self::DELIMITER_AT_EVERY, $delimiterAtEvery);
    }

    /**
     * {@inheritdoc}
     */
    public function getDelimiter()
    {
        return $this->getData(self::DELIMITER);
    }

    /**
     * {@inheritdoc}
     */
    public function setDelimiter($delimiter)
    {
        return $this->setData(self::DELIMITER, $delimiter);
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
        \Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
