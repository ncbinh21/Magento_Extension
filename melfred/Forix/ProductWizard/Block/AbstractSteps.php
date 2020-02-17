<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 05/06/2018
 * Time: 23:42
 */

namespace Forix\ProductWizard\Block;
use Forix\ProductWizard\Block\Context;
use Forix\ProductWizard\Model\Source\Templates as GroupItemTemplate;
/**
 * Class AbstractSteps
 * @method array getWizardOptions
 * @package Forix\ProductFinder\Block
 */
abstract class AbstractSteps extends \Magento\Framework\View\Element\Template
{
    protected $_groupFactory;
    protected $_groupCollection = [];
    protected $_registry;

    /**
     * AbstractSteps constructor.
     * @param Context $context
     * @param \Forix\ProductWizard\Model\GroupFactory $groupFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Forix\ProductWizard\Model\GroupFactory $groupFactory,
        \Magento\Framework\Registry $registry,
        array $data = [])
    {
        $this->_groupFactory = $groupFactory;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Forix\ProductWizard\Model\Wizard
     */
    public function getCurrentWizard()
    {
        return $this->_registry->registry('current_wizard');
    }

    /**
     * @return array
     */
    public function getDefaultData(){
        $params = $this->getRequest()->getParams();
        if($savedKey = $this->getRequest()->getParam('key')){

        }
        return $params;
    }

    /**
     * @return int 
     */
    public function getStepIndex(){
        return (int)$this->getData('index');
    }

    /**
     * @param $index
     * @return AbstractSteps
     */
    public function setStepIndex($index){
        return $this->setData('index', $index);
    }

    /**
     * @return mixed
     */
    public function getGroups(){
        $key = $this->getStepIndex() . "-" . $this->getCurrentWizard()->getId();
        if(!isset($this->_groupCollection[$key])){
            /**
             * @var $collection \Forix\ProductWizard\Model\ResourceModel\Group\Collection
             */
            $collection = $this->_groupFactory->create()->getCollection();
            $collection->addStepToFilter($this->getStepIndex());
            $collection->addFieldToFilter('wizard_id', $this->getCurrentWizard()->getId());
            $this->_groupCollection[$key] = $collection;
        }
        return $this->_groupCollection[$key];
    }
    

    /**
     * @param \Forix\ProductWizard\Model\GroupItem $groupItem 
     */
    public function getGroupItemRenderer($groupItem){
        /**
         * @var $groupItemBlock \Forix\ProductWizard\Block\AbstractView
         */
        $groupItemBlock = $this->_layout->createBlock('\Forix\ProductWizard\Block\Wizard\GroupItem', 'group.item.renderer.'. ($groupItem->getId()));
        $groupItemBlock->setTemplate(GroupItemTemplate::getTemplateFile($groupItem->getTemplate()));
        $groupItemBlock->setSource($groupItem);
        $groupItemBlock->setStepIndex($this->getStepIndex());
        return $groupItemBlock;
    }
}