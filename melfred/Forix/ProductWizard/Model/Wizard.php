<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api\Data\WizardInterface;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\ResourceModel\Page as ResourceCmsPage;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Cms\Helper\Page as PageHelper;

/**
 * Class Wizard
 * @method \Forix\ProductWizard\Model\ResourceModel\Wizard getResource()
 * @package Forix\ProductWizard\Model
 */
class Wizard extends \Magento\Framework\Model\AbstractModel implements WizardInterface
{

    /**
     * No route page id
     */
    const NOROUTE_WIZARD_ID = 'no-route';

    /**#@+
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_eventPrefix = 'forix_productwizard_wizard';


    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    protected $_availableSkus;
    protected $_availableProduct;
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\ProductWizard\Model\ResourceModel\Wizard');
    }

    /**
     * @return \Magento\Catalog\Model\ProductRender[]
     */
    public function getAvailableProductCollection(){
        if(!$this->_availableProduct){
            $this->_availableProduct = $this->getResource()->getAvailableProductCollection($this->getAvailableProductSku());
        }
        return $this->_availableProduct;
    }

    /**
     * @return array
     */
    public function getAvailableProductSku(){
        if(!$this->_availableSkus) {
            $this->_availableSkus = $this->getResource()->getAvailableProductSku($this);
        }
        return $this->_availableSkus;
    }

    /**
     * @param string $attributeCode
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl($attributeCode = 'base_image')
    {
        $url = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (is_string($image)) {
                $url = $this->getResource()->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . 'configurator/wizard/' . $image;
            } elseif (is_array($image) && isset($image[0]) && isset($image[0]['name'])) {
                $url = $image[0]['name'];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    public function getRequiredItemSets(){
        $requiredItemSets = $this->getScopeConfig()->getValue('wizard/'. str_replace('-', '_', $this->getIdentifier())  .'/required_item_set');
        if ($requiredItemSets) {
            return array_values(json_decode($requiredItemSets, true));
        }
        return [];
    }
    /**
     * Get wizard_id
     * @return string
     */
    public function getWizardId()
    {
        return $this->getData(self::WIZARD_ID);
    }

    /**
     * Set wizard_id
     * @param string $wizardId
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setWizardId($wizardId)
    {
        return $this->setData(self::WIZARD_ID, $wizardId);
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
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get category_id
     * @return string
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Set category_id
     * @param string $categoryId
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Get identifier
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * Set identifier
     * @param string $identifier
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * Get template_update
     * @return string
     */
    public function getTemplateUpdate()
    {
        return $this->getData(self::TEMPLATE_UPDATE);
    }

    /**
     * Set template_update
     * @param string $templateUpdate
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setTemplateUpdate($templateUpdate)
    {
        return $this->setData(self::TEMPLATE_UPDATE, $templateUpdate);
    }


    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Page
     *
     * @return \Magento\Cms\Model\Page
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_WIZARD_ID, $this->getIdFieldName());
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }


    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function beforeSave()
    {
        $originalIdentifier = $this->getOrigData('identifier');
        $currentIdentifier = $this->getIdentifier();

        if (!$this->getId() || $originalIdentifier === $currentIdentifier) {
            return parent::beforeSave();
        }

        switch ($originalIdentifier) {
            case $this->getScopeConfig()->getValue(PageHelper::XML_PATH_NO_ROUTE_PAGE):
                throw new LocalizedException(
                    __('This identifier is reserved for "CMS No Route Page" in configuration.')
                );
            case $this->getScopeConfig()->getValue(PageHelper::XML_PATH_HOME_PAGE):
                throw new LocalizedException(__('This identifier is reserved for "CMS Home Page" in configuration.'));
            case $this->getScopeConfig()->getValue(PageHelper::XML_PATH_NO_COOKIES_PAGE):
                throw new LocalizedException(
                    __('This identifier is reserved for "CMS No Cookies Page" in configuration.')
                );
        }

        return parent::beforeSave();
    }

    /**
     * @return ScopeConfigInterface
     */
    private function getScopeConfig()
    {
        if (null === $this->scopeConfig) {
            $this->scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        }

        return $this->scopeConfig;
    }

    /**
     * Get is_active
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param int $isActive
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    protected function getDataArray($key)
    {
        if ($this->getData($key)) {
            if (!is_array($this->getData($key))) {
                $this->setData($key, explode(',', $this->getData($key)));
            }
        } else {
            $this->setData($key, []);
        }
        return $this->_getData($key);
    }

    /**
     * @return \Forix\ProductWizard\Model\ResourceModel\Group\Collection
     */
    public function getGroups()
    {
        return $this->getResource()->getGroups($this);
    }

    /**
     * Get back_to
     * @return string|null
     */
    public function getBackTo()
    {
        return $this->getData(self::BACK_TO);
    }

    /**
     * Set back_to
     * @param string $backTo
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBackTo($backTo)
    {

        return $this->setData(self::BACK_TO, $backTo);
    }

    /**
     * Get back_to_title
     * @return string|null
     */
    public function getBackToTitle()
    {
        return $this->getData(self::BACK_TO_TITLE);
    }

    /**
     * Set back_to_title
     * @param string $backToTitle
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBackToTitle($backToTitle)
    {
        return $this->setData(self::BACK_TO_TITLE, $backToTitle);
    }


    /**
     * @inheritdoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get attr_set_warning_message
     * @return string|null
     */
    public function getAttrSetWarningMessage()
    {
        return $this->getData(self::ATTR_SET_WARNING_MESSAGE);
    }

    /**
     * Set attr_set_warning_message
     * @param string $attrSetWarningMessage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setAttrSetWarningMessage($attrSetWarningMessage)
    {
        return $this->setData(self::ATTR_SET_WARNING_MESSAGE, $attrSetWarningMessage);
    }

    /**
     * Get config_group
     * @return string|null
     */
    public function getConfigGroup()
    {

        return $this->getData(self::CONFIG_GROUP);
    }

    /**
     * Set config_group
     * @param string $configGroup
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setConfigGroup($configGroup)
    {

        return $this->setData(self::CONFIG_GROUP, $configGroup);
    }

    /**
     * Get base_image
     * @return string|null
     */
    public function getBaseImage()
    {
        return $this->getData(self::BASE_IMAGE);
    }

    /**
     * Set base_image
     * @param string $baseImage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBaseImage($baseImage)
    {
        return $this->setData(self::BASE_IMAGE, $baseImage);
    }

    /**
     * Get banner_image
     * @return string|null
     */
    public function getBannerImage()
    {
        return $this->getData(self::BANNER_IMAGE);
    }

    /**
     * Set banner_image
     * @param string $bannerImage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setBannerImage($bannerImage)
    {
        return $this->setData(self::BANNER_IMAGE, $bannerImage);
    }

    /**
     * Get skip_notification_message
     * @return string|null
     */
    public function getSkipNotificationMessage()
    {
        return $this->getData(self::SKIP_NOTIFICATION_MESSAGE);
    }

    /**
     * Set skip_notification_message
     * @param string $skipNotificationMessage
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setSkipNotificationMessage($skipNotificationMessage)
    {
        return $this->setData(self::SKIP_NOTIFICATION_MESSAGE, $skipNotificationMessage);
    }

    /**
     * Get step_count
     * @return int|null
     */
    public function getStepCount()
    {
        return $this->getData(self::STEP_COUNT);
    }

    /**
     * Set step_count
     * @param int $stepCount
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function setStepCount($stepCount)
    {
        return $this->setData(self::STEP_COUNT, $stepCount);
    }
}
