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

class Wizard implements SourceInterface, OptionSourceInterface, \Magento\Framework\Option\ArrayInterface
{
    protected $_collectionFactory;

    protected $_optionHash;

    public function __construct(
        \Forix\ProductWizard\Model\ResourceModel\Wizard\CollectionFactory $collectionFactory
    )
    {
        $this->_collectionFactory = $collectionFactory;
    }
    /**
     * Return array of options as value-label pairs
     *
     * @return array 
     */
    public function getOptionArray()
    {
        if (null === $this->_optionHash) {
            /**
             * @var $items \Forix\ProductWizard\Model\ResourceModel\Wizard\Collection
             * @var $item \Forix\ProductWizard\Model\Wizard
             * 
             */
            $items =  $this->_collectionFactory->create();
            $tempData = [];
            foreach ($items as $item) {
                $tempData[$item->getId()] = $item->getTitle();
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