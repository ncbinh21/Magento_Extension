<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 03/07/2018
 * Time: 14:58
 */
namespace Forix\ProductWizard\Model\Source\Product;

use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
class AttributeSet  implements SourceInterface, OptionSourceInterface
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory 
     */
    protected $collectionFactory;
    
    /**
     * @var null|array
     */
    protected $_optionHash;
    
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $product
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
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
             * @var $items \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection
             * @var $item \Magento\Eav\Model\Entity\Attribute\Set
             */
            $items = $this->collectionFactory->create()
                ->setEntityTypeFilter($this->product->getTypeId());
            
            $tempData['0'] = __('--- Select Options ---');
            foreach ($items as $item) {
                $tempData[$item->getId()] = $item->getData('attribute_set_name');
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