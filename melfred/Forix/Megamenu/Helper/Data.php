<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 03/10/2018
 * Time: 11:07
 */
namespace Forix\Megamenu\Helper;

use \Magento\Catalog\Model\Product;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as AmastyBrandCollection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const RIG_MODEL_ATTRIBUTE = 'mb_rig_model';


    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;
    /**
     * @var \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory
     */
    protected $_advAttrCollectionFactory;


    protected $_advAttributeCollection;

    protected $_optionManufacturer;

    protected $_optionSetting;

    protected $_amastyOptionSetting = [];
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory $advAttrCollectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Forix\AdvancedAttribute\Model\Config\Source\OptionManufacturer $optionManufacturer,
        \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory $advAttrCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        AmastyBrandCollection $optionSetting
    )
    {
        $this->_eavConfig = $eavConfig;
        $this->_advAttrCollectionFactory = $advAttrCollectionFactory;
        $this->_optionManufacturer = $optionManufacturer;
        $this->_optionSetting = $optionSetting;
        parent::__construct($context);
    }

    /**
     * @param $attrCode
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductAttribute($attrCode){

        return $this->_eavConfig->getAttribute(Product::ENTITY, $attrCode);
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRigModelAttribute(){
        return $this->getProductAttribute(self::RIG_MODEL_ATTRIBUTE);
    }

    /**
     * @param $attributeId
     * @return \Forix\AdvancedAttribute\Model\ResourceModel\Option\Collection
     */
    protected function getAdvAttrOptionCollection($attributeId){
        if(!isset($this->_advAttributeCollection[$attributeId])){
            $collection = $this->_advAttrCollectionFactory->create();
            $collection->addFieldToFilter('attribute_id', $attributeId);
            $this->_advAttributeCollection[$attributeId] = $collection;
        }
        return $this->_advAttributeCollection[$attributeId];
    }
    /**
     * @param $attributeId
     * @param $optionId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getManufactureLabelBy($attributeId, $optionId){

        $attrAttributeOptions = $this->getAdvAttrOptionCollection($attributeId);
        $manufactureValue = null;
        foreach ($attrAttributeOptions as $option){
            if($option->getOptionId() == $optionId){
                $option->getOptionId();
                $manufactureValue = $option->getData('mb_oem');
                break;
            }
        }

        if($manufactureValue){
            return str_replace([" ","/"],["-","_"],$this->_optionManufacturer->getOptionText($manufactureValue));
        }
        return '';
    }

    public function getAmastyOptionSetting($value) {
        if(!isset($this->_amastyOptionSetting[$value])) {
            $this->_amastyOptionSetting[$value] = '';
            $collection = $this->_optionSetting->create();
            $_value = $collection->addFieldToFilter('value', $value);
            $data = $_value->getFirstItem();
            $rigManufacture = $data->getData('rig_manufacture');
            if ($rigManufacture) {
                $this->_amastyOptionSetting[$value] = str_replace([" ", "/"], ["-", "_"], $this->_optionManufacturer->getOptionText($rigManufacture));
            }
        }
        return $this->_amastyOptionSetting[$value];
    }
}