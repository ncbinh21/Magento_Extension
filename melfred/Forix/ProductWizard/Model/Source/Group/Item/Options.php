<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/06/2018
 * Time: 16:47
 */

namespace Forix\ProductWizard\Model\Source\Group\Item;


use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

class Options implements SourceInterface, OptionSourceInterface
{

    protected $_groupItemOptionFactory;
    protected $_groupItemFactory;
    protected $_optionHash = null;
    protected $_groupItemHash = null;
    protected $_coreRegistry;
    protected $_collection = null;
    public function __construct(
        \Forix\ProductWizard\Model\GroupItemOptionFactory $groupItemOptionFactory,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_groupItemOptionFactory = $groupItemOptionFactory;
        $this->_groupItemFactory = $groupItemFactory;
    }


    /**
     * @return \Forix\ProductWizard\Model\GroupItemOption|null
     */
    public function getCurrentOptions(){
        return $this->_coreRegistry->registry('forix_productwizard_group_item_option');
    } 
    
    public function getOptionCollection(){
        if(null === $this->_collection) {

            $groupItemCollection = $this->_groupItemFactory->create()->getCollection();
            $this->_groupItemHash = $groupItemCollection->toOptionHash();
            
            $this->_collection = $this->_groupItemOptionFactory->create()->getCollection();
            if ($item = $this->getCurrentOptions()) {
                $this->_collection->addFieldToFilter('group_item_option_id', ['nin' => [$item->getId()]]);
            }
        }
        return $this->_collection;
    }
    
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getOptionArray()
    {
        if(null === $this->_optionHash) {
            /**
             * @var $collection \Forix\ProductWizard\Model\ResourceModel\GroupItemOption\Collection
             */
            $collection = $this->getOptionCollection();
            $this->_optionHash = $collection->toOptionHash();
        }
        return $this->_optionHash;
    }
    
    protected function getGroup($groups, $groupId){
        $currentItem = $this->getCurrentOptions();
        foreach ($groups as $index => $group){
            if($group['index'] == $groupId){
                if($currentItem){
                    if($groupId == $currentItem->getItemId()){
                        return -1;
                    }
                }
                return $index;
            }
        }
        return -1;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];
        $optionsCollection = $this->getOptionCollection();
        foreach ($this->_groupItemHash as $id => $label){
            $result[] = ['value' => [], 'index' => $id, 'label' => $label];
        }
        
        foreach ($optionsCollection as $option){
            $itemId = $option->getItemId();
            $groupIndex = $this->getGroup($result, $itemId);
            if(-1 != $groupIndex){
                $result[$groupIndex]['value'][] = ['value' => $itemId. "_".$option->getId(), 'label' => $option->getTitle()];
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