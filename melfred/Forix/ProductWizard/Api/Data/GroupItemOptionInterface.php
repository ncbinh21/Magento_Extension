<?php


namespace Forix\ProductWizard\Api\Data;

interface GroupItemOptionInterface
{

    const GROUP_ITEM_OPTION_ID = 'group_item_option_id';
    const DEPEND_ON = 'depend_on';
    const OPTION_VALUE = 'option_value';
    const PRODUCT_SKU = 'product_sku';
    const BEST_ON = 'best_on';
    const SELECT_TYPE = 'select_type';
    const OPTION_ID = 'option_id';
    const ITEM_ID = 'item_id';
    const ITEM_SET_ID = 'item_set_id';


    /**
     * Get group_item_option_id
     * @return string|null
     */
    public function getGroupItemOptionId();

    /**
     * Set group_item_option_id
     * @param string $groupItemOptionId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setGroupItemOptionId($groupItemOptionId);

    /**
     * Get option_id
     * @return string|null
     */
    public function getOptionId();

    /**
     * Set option_id
     * @param string $optionId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setOptionId($optionId);

    /**
     * Get product_sku
     * @return string|null
     */
    public function getProductSku();

    /**
     * Set product_sku
     * @param string $productSku
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setProductSku($productSku);

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param string $itemId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setItemId($itemId);
    /**
     * Get item_set_id
     * @return string|null
     */
    public function getItemSetId();

    /**
     * Set item_set_id
     * @param string $itemSetId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setItemSetId($itemSetId);

    /**
     * Get depend_on
     * @return string|null
     */
    public function getDependOn();

    /**
     * Set depend_on
     * @param string $dependOn
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setDependOn($dependOn);

    /**
     * Get best_on
     * @return string|null
     */
    public function getBestOn();

    /**
     * Set best_on
     * @param string $bestOn
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     */
    public function setBestOn($bestOn);

}
