<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 29/08/2018
 * Time: 15:52
 */

namespace Forix\ProductWizard\Model\ProductRender;

use \Forix\ProductWizard\Api\Data\ProductRender\AttributeOptionDataInterface;

class AttributeOptionData extends \Magento\Framework\Model\AbstractExtensibleModel implements AttributeOptionDataInterface
{
    /**
     * @return string
     * @since 101.1.0
     */
    public function getAttributeCode()
    {
        return $this->getData('attribute_code');
    }

    /**
     * @return string
     */
    public function getAttributeLabel()
    {
        return $this->getData('attribute_label');
    }

    /**
     * @return string
     */
    public function getAttributeValues()
    {
        return $this->getData('attribute_values');
    }

    /**
     * @param string $code
     * @return void
     */
    public function setAttributeCode($code)
    {
        $this->setData('attribute_code', $code);
    }

    /**
     * @param string $label
     * @return void
     */
    public function setAttributeLabel($label)
    {
        $this->setData('attribute_label', $label);
    }

    /**
     * @param string $value
     * @return void
     */
    public function setAttributeValues($value)
    {
        $this->setData('attribute_values', $value);
    }
}