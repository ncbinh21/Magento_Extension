<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 28/06/2018
 * Time: 11:10
 */
namespace Forix\ProductWizard\Model\Source\Category;

use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

class CategoryList implements SourceInterface, OptionSourceInterface
{
    protected $_optionHash;
    protected $_categoryCollectionFactory;
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    )
    {
        $this->_categoryCollectionFactory = $collectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function getOptionArray()
    {
        if(null === $this->_optionHash) {
            /**
             * @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection
             * @var $category \Magento\Catalog\Model\Category
             */
            $collection = $this->_categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name');
            $res = [];
            foreach ($collection as $category) {
                $res[$category->getId()] = $category->getName();
            }
            $this->_optionHash = $res;
        }
        return ['' => __('--- Select Options ---')] + $this->_optionHash;
    }

    /**
     * Retrieve option array with empty value
     * Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @return string[]
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
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}