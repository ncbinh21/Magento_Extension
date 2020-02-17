<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api\Data\GroupItemInterface;
use \Forix\ProductWizard\Model\Source\Templates as GroupItemTemplates;

/**
 * Class GroupItem
 * @method \Forix\ProductWizard\Model\ResourceModel\GroupItem\Collection getCollection()
 * @method \Forix\ProductWizard\Model\ResourceModel\GroupItem getResource()
 * @package Forix\ProductWizard\Model
 */
class GroupItem extends \Magento\Framework\Model\AbstractModel implements GroupItemInterface
{

    protected $_eventPrefix = 'forix_productwizard_group_item';
    const GROUP = 'group';

    /**
     * @param Group $group
     * @return ResourceModel\GroupItem\Collection
     */
    public function getGroupItemsBy(Group $group)
    {
        return $this->getCollection()->addGroupIdToFilter($group->getId());
    }

    /**
     * @return bool
     */
    public function isEnableFindOption(){
        return !!$this->getData('enable_find_option');
    }
    /**
     * @return ResourceModel\GroupItemOption\Collection|\Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    /*public function getOptionCollection()
    {

        $optionCollection = $this->getResource()->getOptions($this);
        switch ($this->getTemplate()) {
            case GroupItemTemplates::DROP_DOWN:
                return $optionCollection;
                break;
            case GroupItemTemplates::PRODUCT_DETAIL_SELECT:
            case GroupItemTemplates::PRODUCT_DETAIL_CHECKBOX:
                $productSku = [];
                foreach ($optionCollection as $option) {
                    $productSku[] = $option->getProductSku();
                }
                $collection = $this->_productCollectionFactory->create();
                $collection->addFieldToFilter('sku', ['in' => $productSku]);
                return $collection;
        }
        return null;
    }*/

    public function getOptionCollection()
    {
        return $this->getResource()->getOptions($this);
    }


    public function getProductSkus()
    {
        $options = $this->getResource()->getOptions($this);
        $result = [];
        foreach ($options as $option) {
            if ($option->getProductSku()) {
                array_push($result, $option->getProductSku());
            }
        }
        return $result;
    }

    public function getProductIds()
    {
        $options = $this->getResource()->getOptions($this);
        $result = [];
        $skuMaps = [];
        foreach ($options as $option) {
            if ($option->getProductSku() && $option->getProductId()) {
                array_push($result, $option->getProductId());
            } else {
                array_push($skuMaps, $option->getProductSku());
            }
        }
        if (!empty($skuMaps)) {
            $sql = "select entity_id from catalog_product_entity where sku in ('" . (implode("','", $skuMaps)) . "')";
            $entityIds = $this->getResource()->getConnection()->fetchCol($sql);
            $result = array_merge($result, $entityIds);
        }
        return $result;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\ProductWizard\Model\ResourceModel\GroupItem');
    }

    /**
     * Get group_id
     * @return Group
     */
    public function getGroup()
    {
        return $this->getData(self::GROUP);
    }

    /**
     * Set group
     * @param Group $group
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setGroup($group)
    {
        return $this->setData(self::GROUP, $group);
    }

    /**
     * Get group_item_id
     * @return string
     */
    public function getGroupItemId()
    {
        return $this->getData(self::GROUP_ITEM_ID);
    }

    /**
     * Set group_item_id
     * @param string $groupItemId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setGroupItemId($groupItemId)
    {
        return $this->setData(self::GROUP_ITEM_ID, $groupItemId);
    }


    /**
     * Get group_id
     * @return string
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * Set group_id
     * @param string $groupId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
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
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get note
     * @return string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * Set note
     * @param string $note
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setNote($note)
    {
        return $this->setData(self::NOTE, $note);
    }

    /**
     * Get sort_order
     * @return string
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set sort_order
     * @param string $sortOrder
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get template
     * @return string
     */
    public function getTemplate()
    {
        return $this->getData(self::TEMPLATE);
    }

    /**
     * Set template
     * @param string $template
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setTemplate($template)
    {
        return $this->setData(self::TEMPLATE, $template);
    }

    /**
     * Get attribute_code
     * @return string|null
     */
    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    /**
     * Set attribute_code
     * @param string $attributeCode
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->setData(self::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * Get is_required
     * @return bool|null
     */
    public function getIsRequired()
    {
        return !!$this->getData(self::IS_REQUIRED);
    }

    /**
     * Set is_required
     * @param bool $isRequired
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setIsRequired($isRequired)
    {
        return $this->setData(self::IS_REQUIRED, $isRequired);
    }

    /**
     * Get next_to
     * @return string|'default'
     */
    public function getNextTo()
    {
        return $this->getData(self::NEXT_TO) ?: 'default';
    }

    /**
     * Set next_to
     * @param string $nextTo
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setNextTo($nextTo)
    {
        return $this->setData(self::NEXT_TO, $nextTo);
    }

    /**
     * Get attribute_set_id
     * @return int|null
     */
    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * Set attribute_set_id
     * @param int $attributeSetId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * Get select_type
     * @return int|null
     */
    public function getSelectType()
    {
        return $this->getData(self::SELECT_TYPE);
    }

    /**
     * Set select_type
     * @param int $selectType
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setSelectType($selectType)
    {
        return $this->setData(self::SELECT_TYPE, $selectType);
    }

    /**
     * Get is_show_view_all
     * @return bool|null
     */
    public function getIsShowViewAll()
    {
        return $this->getData(self::IS_SHOW_VIEW_ALL);
    }

    /**
     * Set is_show_view_all
     * @param bool $isShowViewAll
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setIsShowViewAll($isShowViewAll)
    {
        return $this->setData(self::IS_SHOW_VIEW_ALL, $isShowViewAll);
    }

    /**
     * Get show_all_message
     * @return string
     */
    public function getShowAllMessage()
    {
        return $this->getData(self::SHOW_ALL_MESSAGE);
    }

    /**
     * Set show_all_message
     * @param string $showAllMessage
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setShowAllMessage($showAllMessage)
    {
        return $this->setData(self::SHOW_ALL_MESSAGE, $showAllMessage);
    }

    /**
     * Get button_text
     * @return string
     */
    public function getButtonText()
    {
        return $this->getData(self::BUTTON_TEXT);
    }

    /**
     * Set button_text
     * @param string $buttonText
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setButtonText($buttonText)
    {
        return $this->setData(self::BUTTON_TEXT, $buttonText);
    }
}
