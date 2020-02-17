<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api\Data\GroupItemOptionInterface;

/**
 * Class GroupItemOption
 * @method \Forix\ProductWizard\Model\ResourceModel\GroupItemOption\Collection getCollection()
 * @package Forix\ProductWizard\Model
 */
class GroupItemOption extends \Magento\Framework\Model\AbstractModel implements GroupItemOptionInterface
{

    protected $_eventPrefix = 'forix_productwizard_group_item_option';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\ProductWizard\Model\ResourceModel\GroupItemOption');
    }

    /**
     * @param $option
     * @return \Forix\ProductWizard\Api\Data\WizardInterface|\Forix\ProductWizard\Model\Wizard|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWizard(){
       return $this->getResource()->getWizard($this);
    }

    /**
     * Get group_item_option_id
     * @return string
     */
    public function getGroupItemOptionId()
    {
        return $this->getData(self::GROUP_ITEM_OPTION_ID);
    }

    /**
     * Set group_item_option_id
     * @param string $groupItemOptionId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setGroupItemOptionId($groupItemOptionId)
    {
        return $this->setData(self::GROUP_ITEM_OPTION_ID, $groupItemOptionId);
    }
    /**
     * Get option_id
     * @return string
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * Set option_id
     * @param string $optionId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * Get product_sku
     * @return string
     */
    public function getProductSku()
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * Set product_sku
     * @param string $productSku
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Get item_id
     * @return string
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * Set item_id
     * @param string $itemId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
    
    protected function getDataArray($key){
        if($this->getData($key)){
            if(!is_array($this->getData($key))) {
                $this->setData($key, explode(',', $this->getData($key)));
            }
        } else {
            $this->setData($key, []);
        }
        return $this->_getData($key);
    }
    
    /**
     * Get depend_on
     * @return array
     */
    public function getDependOn()
    {
        return $this->getDataArray(self::DEPEND_ON);
    }

    /**
     * Set depend_on
     * @param string|array $dependOn
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setDependOn($dependOn)
    {
        return $this->setData(self::DEPEND_ON, $dependOn);
    }

    /**
     * Get best_on
     * @return array
     */
    public function getBestOn()
    {
        return $this->getDataArray(self::BEST_ON);
    }

    /**
     * Set best_on
     * @param string $bestOn
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setBestOn($bestOn)
    {
        return $this->setData(self::BEST_ON, $bestOn);
    }

    /**
     * Get item_set_id
     * @return string|null
     */
    public function getItemSetId()
    {
        return $this->getData(self::ITEM_SET_ID);
    }

    /**
     * Set item_set_id
     * @param string $itemSetId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setItemSetId($itemSetId)
    {
        return $this->setData(self::ITEM_SET_ID, $itemSetId);
    }
}
