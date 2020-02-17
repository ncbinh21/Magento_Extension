<?php


namespace Forix\ProductWizard\Api\Data;

interface GroupItemInterface
{

    const GROUP_ITEM_ID = 'group_item_id';
    const NOTE = 'note';
    const TITLE = 'title';
    const TEMPLATE = 'template';
    const SORT_ORDER = 'sort_order';
    const OPTION_ID = 'option_id';
    const SELECT_TYPE = 'select_type';
    const GROUP_ID = 'group_id';
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const NEXT_TO = 'next_to';
    const IS_REQUIRED = 'is_required';
    const IS_SHOW_VIEW_ALL = 'is_show_view_all';
    const SHOW_ALL_MESSAGE = 'show_all_message';
    const BUTTON_TEXT = 'button_text';

    /**
     * Get attribute_set_id
     * @return int|null
     */
    public function getAttributeSetId();

    /**
     * Set attribute_set_id
     * @param int $attributeSetId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setAttributeSetId($attributeSetId);

    /**
     * Get is_required
     * @return bool|null
     */
    public function getIsRequired();

    /**
     * Set is_required
     * @param bool $isRequired
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setIsRequired($isRequired);

    /**
     * Get is_show_view_all
     * @return bool|null
     */
    public function getIsShowViewAll();

    /**
     * Set is_show_view_all
     * @param bool $isShowViewAll
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setIsShowViewAll($isShowViewAll);

    /**
     * Get show_all_message
     * @return string
     */
    public function getShowAllMessage();

    /**
     * Set show_all_message
     * @param string $showAllMessage
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setShowAllMessage($showAllMessage);
    /**
     * Get button_text
     * @return string
     */
    public function getButtonText();

    /**
     * Set button_text
     * @param string $buttonText
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setButtonText($buttonText);

    /**
     * Get next_to
     * @return string|'default'
     */
    public function getNextTo();

    /**
     * Set next_to
     * @param string $nextTo
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setNextTo($nextTo);
    
    /**
     * Get attribute_code
     * @return string|null
     */
    public function getAttributeCode();

    /**
     * Set attribute_code
     * @param string $attributeCode
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setAttributeCode($attributeCode);

    /**
     * Get group_item_id
     * @return string|null
     */
    public function getGroupItemId();

    /**
     * Set group_item_id
     * @param string $groupItemId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setGroupItemId($groupItemId);


    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setGroupId($groupId);

    /**
     * Get option_id
     * @return string|null
     */
    public function getOptionId();

    /**
     * Set option_id
     * @param string $optionId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setOptionId($optionId);

    /**
     * Get select_type
     * @return int|null
     */
    public function getSelectType();

    /**
     * Set select_type
     * @param int $selectType
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setSelectType($selectType);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setTitle($title);

    /**
     * Get note
     * @return string|null
     */
    public function getNote();

    /**
     * Set note
     * @param string $note
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setNote($note);

    /**
     * Get sort_order
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Set sort_order
     * @param string $sortOrder
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Get template
     * @return string|null
     */
    public function getTemplate();

    /**
     * Set template
     * @param string $template
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     */
    public function setTemplate($template);
}
