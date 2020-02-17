<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/06/2018
 * Time: 17:35
 */

namespace Forix\ProductWizard\Model\Source\Group;


use Forix\ProductWizard\Model\GroupFactory;
use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

class Items implements SourceInterface, OptionSourceInterface
{
    
    protected $_groupItemFactory;
    protected $_groupFactory;
    protected $_optionHash = null;
    protected $_optionArrayHash = null;
    public function __construct(
        \Forix\ProductWizard\Model\GroupFactory $groupFactory,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory
    )
    {
        $this->_groupFactory = $groupFactory;
        $this->_groupItemFactory = $groupItemFactory;
    }

    public function getOptionArray(){
        $results = [];
        foreach (self::getAllOptionArray() as $value){
            $groupItems = $value['group_item'];
            foreach ($groupItems as $_index => $_value) {
                $results[$_index] = $_value;
            }
        }
        return $results;
    }
    /**
     * Return array of options as value-label pairs
     *
     */
    protected function getAllOptionArray()
    {
        if(null === $this->_optionHash) {
            $this->_optionHash = [];
            /**
             * @var $collection \Forix\ProductWizard\Model\ResourceModel\GroupItem\Collection
             * @var $group \Forix\ProductWizard\Model\Group
             */
            $groupCollection = $this->_groupFactory->create()->getCollection();
            foreach ($groupCollection as $group) {
                $collection = $group->getGroupItemCollection();
                $this->_optionHash[] = [
                    'group' => $group,
                    'group_item' => $collection->toOptionHash()
                ];
            }
        }
        return $this->_optionHash;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getAllOptionArray() as $value) {
            if(isset($value['group'])) {
                $group = $value['group'];
                $groupItems = $value['group_item'];
                $values = [];
                foreach ($groupItems as $_index => $_value) {
                    $values[] = ['value' => $_index, 'label' => $_value];
                }
                $result[] = ['value' => $values, 'index' => $group->getId(), 'label' => $group->getTitle()];
            }
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
        $options = self::getAllOptionArray();
        foreach($options as $value) {
            $groupItems = $value['group_item'];
            if(isset($groupItems[$optionId])){
                return $groupItems[$optionId];
            }
        }
        return null;
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