<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 13/06/2018
 * Time: 10:33
 */

namespace Forix\ProductWizard\Model\Source;


use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

class Attribute implements SourceInterface, OptionSourceInterface
{
    protected $_eavConfig;

    protected $_optionHash;

    public function __construct(
        \Magento\Eav\Model\Config $_eavConfig
    )
    {
        $this->_eavConfig = $_eavConfig;
    }


    /**
     * Return array of options as value-label pairs
     *
     * @return array 
     */
    public function getOptionArray()
    {
        if (null === $this->_optionHash) {
            $items =  $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getAttributeCollection()
                ->addFieldToFilter('is_user_defined',array('eq' => 1 ))
                ->addFieldToFilter('frontend_input', array('in' => array('select', 'multiselect')))
                ->setOrder('frontend_label')
                ->toArray()["items"];
            
            $tempData['0'] = __('--- Select Options ---');
            foreach ($items as $item) {
                $tempData[$item["attribute_code"]] = $item["frontend_label"];
            }
            $this->_optionHash = $tempData;
        }
        return $this->_optionHash;
    }


    /**
     * Retrieve option array with empty value
     * Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     *  Retrieve Option value text
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array 
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}