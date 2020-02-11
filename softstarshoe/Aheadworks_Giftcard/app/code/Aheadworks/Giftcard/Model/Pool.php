<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model;

use Aheadworks\Giftcard\Api\Data\PoolInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Giftcard\Model\ResourceModel\Pool as ResourcePool;

/**
 * Class Pool
 *
 * @package Aheadworks\Giftcard\Model
 */
class Pool extends AbstractModel implements PoolInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourcePool::class);
    }

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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeLength()
    {
        return $this->getData(self::CODE_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeLength($codeLength)
    {
        return $this->setData(self::CODE_LENGTH, $codeLength);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeFormat()
    {
        return $this->getData(self::CODE_FORMAT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeFormat($codeFormat)
    {
        return $this->setData(self::CODE_FORMAT, $codeFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodePrefix()
    {
        return $this->getData(self::CODE_PREFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodePrefix($codePrefix)
    {
        return $this->setData(self::CODE_PREFIX, $codePrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeSuffix()
    {
        return $this->getData(self::CODE_SUFFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeSuffix($codeSuffix)
    {
        return $this->setData(self::CODE_SUFFIX, $codeSuffix);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeDelimiterAtEvery()
    {
        return $this->getData(self::CODE_DELIMITER_AT_EVERY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeDelimiterAtEvery($codeDelimiterAtEvery)
    {
        return $this->setData(self::CODE_DELIMITER_AT_EVERY, $codeDelimiterAtEvery);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\Giftcard\Api\Data\PoolExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
