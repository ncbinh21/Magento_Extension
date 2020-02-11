<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Template\Rule\Condition;

use Magento\Eav\Model\ResourceModel\Entity;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Validate extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory
     */
    protected $stockItemFactory;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $entityAttributeSetCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ ResourceModel\Product\Type\ConfigurableFactory
     */
    protected $productTypeConfigurableFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\CatalogInventory\Model\Stock\ItemFactory                   $stockItemFactory
     * @param \Magento\CatalogInventory\Model\StockState                          $stockState
     * @param \Magento\CatalogRule\Model\ResourceModel\RuleFactory                $ruleFactory
     * @param Entity\Attribute\Set\CollectionFactory                              $entityAttributeSetCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory                 $productFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $productTypeConfigurableFactory
     * @param \Magento\Eav\Model\Config                                           $config
     * @param \Magento\Framework\UrlInterface                                     $urlManager
     * @param \Magento\Backend\Model\Url                                          $backendUrlManager
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager
     * @param \Magento\Framework\Locale\FormatInterface                           $localeFormat
     * @param \Magento\Customer\Model\Session                                     $customerSession
     * @param \Magento\Rule\Model\Condition\Context                               $context
     * @param \Magento\Framework\Registry                                         $registry
     * @param array                                                               $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\ItemFactory $stockItemFactory,
        \Magento\CatalogInventory\Model\StockState  $stockState,
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory,
        Entity\Attribute\Set\CollectionFactory $entityAttributeSetCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $productTypeConfigurableFactory,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->stockItemFactory = $stockItemFactory;
        $this->stockState = $stockState;
        $this->ruleFactory = $ruleFactory;
        $this->entityAttributeSetCollectionFactory = $entityAttributeSetCollectionFactory;
        $this->productFactory = $productFactory;
        $this->productTypeConfigurableFactory = $productTypeConfigurableFactory;
        $this->config = $config;
        $this->urlManager = $urlManager;
        $this->backendUrlManager = $backendUrlManager;
        $this->storeManager = $storeManager;
        $this->localeFormat = $localeFormat;
        $this->assetRepo = $context->getAssetRepository();
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->registry = $registry;
        $this->localeDate = $context->getLocaleDate();
        parent::__construct($context, $data);
    }

    /**
     * @var null|string|int
     */
    protected $entityAttributeValues = null;
    /**
     * @var string
     */
    protected $isUsedForRuleProperty = 'is_used_for_promo_rules';

    /**
     * @return false|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute|\Magento\Framework\DataObject
     */
    public function getAttributeObject()
    {
        try {
            $obj = $this->config
                ->getAttribute('catalog_product', $this->getAttribute());
        } catch (\Exception $e) {
            $obj = new \Magento\Framework\DataObject();
            $obj->setEntity($this->productFactory->create())
                ->setFrontendInput('text');
        }

        return $obj;
    }

    /**
     * @param array &$attributes
     *
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['attribute_set_id'] = __('Attribute Set');
        $attributes['category_ids'] = __('Category');
        $attributes['created_at'] = __('Created At (days ago)');
        $attributes['updated_at'] = __('Updated At (days ago)');
        $attributes['qty'] = __('Quantity');
        $attributes['price_diff'] = __('Price - Final Price');
        $attributes['percent_discount'] = __('Percent Discount');
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->productFactory->create()
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = [];
        foreach ($productAttributes as $attribute) {
            if (!$attribute->isAllowedForRuleCondition()
                || !$attribute->getDataUsingMethod($this->isUsedForRuleProperty)) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if ($this->getAttribute() === 'attribute_set_id') {
            $entityTypeId = $this->config
                ->getEntityType('catalog_product')->getId();
            $selectOptions = $this->entityAttributeSetCollectionFactory->create()
                ->setEntityTypeFilter($entityTypeId)
                ->load()
                ->toOptionArray();
        } elseif (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                if ($attributeObject->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
            }
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = [];
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Retrieve value by option.
     *
     * @param string $option
     *
     * @return string
     */
    public function getValueOption($option = null)
    {
        $this->_prepareValueOptions();

        return $this->getData('value_option'.($option !== null ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values.
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();

        return $this->getData('value_select_options');
    }

    /**
     * Retrieve after element HTML.
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $image = $this->assetRepo->getUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger">
                    <img src="'.$image.'" alt="" class="v-middle rule-chooser-trigger" title="'.__('Open Chooser').'" />
                    </a>';
        }

        return $html;
    }

    /**
     * Retrieve attribute element.
     *
     * @return Varien_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @param string $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        if (!in_array($attribute, ['category_ids', 'qty', 'price_diff', 'percent_discount'])) {
            if ($this->getAttributeObject()->isScopeGlobal()) {
                $attributes = $this->getRule()->getCollectedAttributes();
                $attributes[$attribute] = true;
                $this->getRule()->setCollectedAttributes($attributes);
                $productCollection->addAttributeToSelect($attribute, 'left');
            } else {
                $this->entityAttributeValues = $productCollection->getAllAttributeValues($attribute);
            }
        } elseif (($attribute == 'price_diff')
                  || ($attribute == 'percent_discount')) {
            $productCollection->addAttributeToSelect('price', 'left');
            $productCollection->addAttributeToSelect('special_price', 'left');
            $productCollection->addAttributeToSelect('special_from_date', 'left');
            $productCollection->addAttributeToSelect('special_to_date', 'left');
            $productCollection->addAttributeToSelect('type_id', 'left');
        }

        return $this;
    }

    /**
     * Retrieve input type.
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    /**
     * Retrieve value element type.
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
            case 'boolean':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            default:
                return 'text';
        }
    }

    /**
     * Retrieve value element chooser URL.
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser'
                .'/attribute/'.$this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                } else {
                    $url .= '/form/rule_conditions_fieldset';
                }
                break;
        }

        return $url !== false ? $this->backendUrlManager->getUrl($url) : '';
    }

    /**
     * Retrieve Explicit Apply.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                return true;
        }

        return false;
    }

    /**
     * Load array.
     *
     * @param array $arr
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Product
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        if ($attribute && $attribute->getBackendType() == 'decimal') {
            if (isset($arr['value'])) {
                if (!empty($arr['operator'])
                    && in_array($arr['operator'], ['!()', '()'])
                    && false !== strpos($arr['value'], ',')) {
                    $tmp = [];
                    foreach (explode(',', $arr['value']) as $value) {
                        $tmp[] = $this->localeFormat->getNumber($value);
                    }
                    $arr['value'] = implode(',', $tmp);
                } else {
                    $arr['value'] = $this->localeFormat->getNumber($arr['value']);
                }
            } else {
                $arr['value'] = false;
            }
            $arr['is_value_parsed'] = isset($arr['is_value_parsed'])
                ? $this->localeFormat->getNumber($arr['is_value_parsed']) : false;
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate product attrbute value for condition.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        $attrCode = $this->getAttribute();

        switch ($attrCode) {
            case 'category_ids':
                return $this->validateCategory($object);

            case 'attribute_set_id':
                $attrId = $object->getAttributeSetId();

                return $this->validateAttribute($attrId);

            case 'qty':
                return $this->validateQty($object);

            default:
                return $this->validateValue($object, $attrCode);
        }
    }

    /**
     * @param string $object
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function validateCategory($object)
    {
        if ($object instanceof \Magento\Catalog\Model\Category) {
            $categoryIds = [$object->getId()];
            $categoryIds = $this->prepareCategoryIds($categoryIds, true, $object);
        } else {
            $categoryIds = $object->getAvailableInCategories();
            $categoryIds = $this->prepareCategoryIds($categoryIds, false, $object);
        }
        $op = $this->getOperatorForValidate();
        if ((($op == '==') || ($op == '!=')) && is_array($categoryIds)) {
            $value = $this->getValueParsed();
            $value = preg_split('#\s*[,;]\s*#', $value, null, PREG_SPLIT_NO_EMPTY);
            $findElemInArray = array_intersect($categoryIds, $value);
            if (count($findElemInArray) > 0) {
                if ($op == '==') {
                    $result = true;
                }
                if ($op == '!=') {
                    $result = false;
                }
            } else {
                if ($op == '==') {
                    $result = false;
                }
                if ($op == '!=') {
                    $result = true;
                }
            }

            return $result;
        }

        return $this->validateAttribute($categoryIds);
    }

    /**
     * @param array $categoryIds
     * @param boolean $isCategory
     * @param \Magento\Catalog\Model\Category $object
     *
     * @return array
     *
     */
    protected function prepareCategoryIds($categoryIds, $isCategory, $object)
    {
        $applyForChildCategories = $this->getRule()->getApplyForChildCategories();
        if ($applyForChildCategories
            && $isCategory
            && ($categoryPath = $object->getPathIds())
            && is_array($categoryPath)) {
            $categoryIds = array_merge($categoryIds, $categoryPath);
        } elseif ($applyForChildCategories
            && !$isCategory
            && ($categoryCollection = $object->getCategoryCollection())
            && is_object($categoryCollection)) {
            foreach ($categoryCollection as $category) {
                $categoryIds = array_merge($categoryIds, $category->getPathIds());
            }
        }

        return array_unique($categoryIds);
    }

    /**
     * @param string $object
     * @param string $attrCode
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function validateValue($object, $attrCode)
    {
        if (!isset($this->entityAttributeValues[$object->getId()])) {
            $attr = $object->getResource()->getAttribute($attrCode);

            if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                $this->setValue(strtotime($this->getValue()));

                $value = strtotime($object->getData($attrCode));

                return $this->validateAttribute($value);
            }

            if ($attr && $attr->getFrontendInput() == 'multiselect') {
                $value = $object->getData($attrCode);
                $value = strlen($value) ? explode(',', $value) : [];

                return $this->validateAttribute($value);
            }

            return parent::validate($object);
        } else {
            $result = false; // any valid value will set it to TRUE
            $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;
            // remember old attribute state
            foreach ($this->entityAttributeValues[$object->getId()] as $value) {
                $attr = $object->getResource()->getAttribute($attrCode);
                if ($attr && $attr->getBackendType() == 'datetime') {
                    $value = strtotime($value);
                } elseif ($attr && $attr->getFrontendInput() == 'multiselect') {
                    $value = strlen($value) ? explode(',', $value) : [];
                }

                $object->setData($attrCode, $value);
                $result |= parent::validate($object);

                if ($result) {
                    break;
                }
            }

            if ($oldAttrValue === null) {
                $object->unsetData($attrCode);
            } else {
                $object->setData($attrCode, $oldAttrValue);
            }

            return (bool) $result;
        }
    }

    /**
     * @param string $object
     *
     * @return bool
     */
    protected function validateQty($object)
    {
        $stockItem = $this->stockItemFactory->create()->setProduct($object);
        if ($object->getTypeId() == 'configurable' && $stockItem->getIsInStock()) {
            $requiredChildrenIds = $this->productTypeConfigurableFactory->create()
                                    ->getChildrenIds($object->getId(), true);
            $childrenIds = [];
            foreach ($requiredChildrenIds as $groupedChildrenIds) {
                $childrenIds = array_merge($childrenIds, $groupedChildrenIds);
            }
            $sumQty = 0;
            foreach ($childrenIds as $childId) {
                $childQty = $this->stockState->getStockQty($childId);
                $sumQty += $childQty;
            }

            return $this->validateAttribute($sumQty);
        } elseif ($object->getTypeId() == 'configurable') {
            return false;
        }

        $qty = $this->stockState->getStockQty($object->getId());

        return $this->validateAttribute($qty);
    }
}
