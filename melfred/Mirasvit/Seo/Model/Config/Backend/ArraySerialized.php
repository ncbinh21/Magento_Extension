<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized as SerializedArraySerialized;

class ArraySerialized extends SerializedArraySerialized
{
    /**
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            $this->setValue(empty($value) ? false : $this->getUnserializedValue($value));
        }
    }

    /**
     * @param string $value
     * @return array
     */
    protected function getUnserializedValue($value)
    {
        if ($value != '[]' && json_decode($value)) { //M2.2 compatibility
            $value = $this->getSerializer()->unserialize($value);
        } elseif ($value != '[]') {
            $value = unserialize($value);
        }

        return $value;
    }

    /**
     * @return \Magento\Framework\Serialize\Serializer\Json|false
     */
    protected function getSerializer()
    {
        $serializer = false;
        if (class_exists(\Magento\Framework\Serialize\Serializer\Json::class)) {
            $serializer = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Serialize\Serializer\Json::class
            );
        }

        return $serializer;
    }
}
