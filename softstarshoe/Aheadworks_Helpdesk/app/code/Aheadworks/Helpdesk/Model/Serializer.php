<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Serializer
 * @package Aheadworks\Helpdesk\Model
 * @codingStandardsIgnoreFile
 */
class Serializer
{
    /**
     * Class name of native Magento serializer
     */
    const JSON_SERIALIZER_CLASS_NAME = 'Magento\Framework\Serialize\Serializer\Json';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var object|null
     */
    private $serializerObject = null;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve serializer object if corresponding class exists
     *
     * @return null|object
     */
    private function getSerializerObject()
    {
        if (empty($this->serializerObject)) {
            if (class_exists(self::JSON_SERIALIZER_CLASS_NAME)) {
                $this->serializerObject = $this->objectManager->get(self::JSON_SERIALIZER_CLASS_NAME);
            }
        }
        return $this->serializerObject;
    }

    /**
     * Serialize data into string
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     */
    public function serialize($data)
    {
        $result = null;
        $serializer = $this->getSerializerObject();
        if ($serializer && is_object($serializer)) {
            $result = $serializer->serialize($data);
        } else {
            $result = $this->getDefaultSerializedData($data);
        }
        return $result;
    }

    /**
     * Get data, serialized in the default way
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     */
    private function getDefaultSerializedData($data)
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }
        return $result;
    }

    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     */
    public function unserialize($string)
    {
        $result = null;
        $serializer = $this->getSerializerObject();
        if ($serializer && is_object($serializer)) {
            $result = $serializer->unserialize($string);
        } else {
            $result = $this->getDefaultUnserializedData($string);
        }
        return $result;
    }

    /**
     * Get data, unserialized in the default way
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     */
    private function getDefaultUnserializedData($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }
}
