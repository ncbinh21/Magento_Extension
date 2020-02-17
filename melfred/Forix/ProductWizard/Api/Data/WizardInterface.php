<?php


namespace Forix\ProductWizard\Api\Data;

interface WizardInterface
{

    const CATEGORY_ID = 'category_id';
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const WIZARD_ID = 'wizard_id';
    const IS_ACTIVE = 'is_active';
    const TEMPLATE_UPDATE = 'template_update';
    const BACK_TO = 'back_to';
    const SORT_ORDER = 'sort_order';
    const BACK_TO_TITLE = 'back_to_title';
    const ATTR_SET_WARNING_MESSAGE = 'attr_set_warning_message';
    const CONFIG_GROUP = 'config_group';
    const BASE_IMAGE = 'base_image';
    const BANNER_IMAGE = 'banner_image';
    const STEP_COUNT = 'step_count';
    const SKIP_NOTIFICATION_MESSAGE = 'skip_notification_message';

    /**
     * Get sort_order
     * @return integer|null
     */
    public function getSortOrder();

    /**
     * Set sort_order
     * @param integer $sortOrder
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setSortOrder($sortOrder);


    /**
     * Get attr_set_warning_message
     * @return string|null
     */
    public function getAttrSetWarningMessage();

    /**
     * Set attr_set_warning_message
     * @param string $attrSetWarningMessage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setAttrSetWarningMessage($attrSetWarningMessage);

    /**
     * Get template_update
     * @return string|null
     */
    public function getTemplateUpdate();

    /**
     * Set template_update
     * @param string $templateUpdate
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setTemplateUpdate($templateUpdate);
    /**
     * Get base_image
     * @return string|null
     */
    public function getBaseImage();

    /**
     * Set base_image
     * @param string $baseImage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBaseImage($baseImage);
    /**
     * Get banner_image
     * @return string|null
     */
    public function getBannerImage();

    /**
     * Set banner_image
     * @param string $bannerImage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBannerImage($bannerImage);
    /**
     * Get wizard_id
     * @return int|null
     */
    public function getWizardId();

    /**
     * Set wizard_id
     * @param int $wizardId
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setWizardId($wizardId);

    /**
     * Get step_count
     * @return int|null
     */
    public function getStepCount();

    /**
     * Set step_count
     * @param int $stepCount
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setStepCount($stepCount);

    /**
     * Get is_active
     * @return int|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param int $isActive
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setIsActive($isActive);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setTitle($title);

    /**
     * Get skip_notification_message
     * @return string|null
     */
    public function getSkipNotificationMessage();

    /**
     * Set skip_notification_message
     * @param string $skipNotificationMessage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setSkipNotificationMessage($skipNotificationMessage);

    /**
     * Get category_id
     * @return string|null
     */
    public function getCategoryId();

    /**
     * Set category_id
     * @param string $categoryId
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Get url_path
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set identifier
     * @param string $identifier
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setIdentifier($identifier);

    /**
     * Get back_to
     * @return string|null
     */
    public function getBackTo();

    /**
     * Set back_to
     * @param string $backTo
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBackTo($backTo);

    /**
     * Get back_to_title
     * @return string|null
     */
    public function getBackToTitle();

    /**
     * Set back_to_title
     * @param string $backToTitle
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBackToTitle($backToTitle);

    /**
     * Get config_group
     * @return string|null
     */
    public function getConfigGroup();

    /**
     * Set config_group
     * @param string $configGroup
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setConfigGroup($configGroup);
}
