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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Template extends \Magento\Rule\Model\AbstractModel
{
    /**
     * @var int
     */
    protected $productId;
    /**
     * @var int
     */
    protected $categoryId;

    const CACHE_TAG = 'seo_template';

    /**
     * @var string
     */
    protected $_cacheTag = 'seo_template';//@codingStandardsIgnoreLine
    /**
     * @var string
     */
    protected $_eventPrefix = 'seo_template';//@codingStandardsIgnoreLine

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @var \Mirasvit\Seo\Model\Template\Rule\Condition\CombineFactory
     */
    protected $templateRuleConditionCombineFactory;

    /**
     * @var \Mirasvit\Seo\Model\Template\Action\CollectionFactory
     */
    protected $ruleActionCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory
     */
    protected $templateCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $resourceIterator;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Mirasvit\Seo\Model\Template\Rule\Condition\CombineFactory      $templateRuleConditionCombineFactory
     * @param \Mirasvit\Seo\Model\Template\Rule\Action\CollectionFactory      $ruleActionCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory                           $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory    $templateCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator                 $resourceIterator
     * @param \Magento\Framework\Model\Context                                $context
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Framework\Data\FormFactory                             $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface            $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null    $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null              $resourceCollection
     * @param array                                                           $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Seo\Model\Template\Rule\Condition\CombineFactory $templateRuleConditionCombineFactory,
        \Mirasvit\Seo\Model\Template\Rule\Action\CollectionFactory $ruleActionCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->templateRuleConditionCombineFactory = $templateRuleConditionCombineFactory;
        $this->ruleActionCollectionFactory = $ruleActionCollectionFactory;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->resourceIterator = $resourceIterator;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Seo\Model\ResourceModel\Template');
    }

    /**
     * @param bool|false $ruleId
     * @return \Mirasvit\Seo\Model\Template
     */
    public function getRule($ruleId = false)
    {
        $ruleId = ($ruleId) ? $ruleId : $this->getId();

        $rule = $this->templateCollectionFactory->create()
            ->addFieldToFilter('template_id', $ruleId)
            ->getFirstItem();
        $rule = $rule->load($rule->getId());

        return $rule;
    }

    /**
     * @return \Mirasvit\Seo\Model\Template\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->templateRuleConditionCombineFactory->create();
    }

    /**
     * @return Template\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->ruleActionCollectionFactory->create();
    }

    /**
     * @param string|array $productId
     * @return bool
     */
    public function isProductApplied($productId)
    {
        $isArray = is_array($productId) ? true : false;
        if ($this->productId === null) {
            $this->setCollectedAttributes([]);
            $condition = ($isArray) ? ['in' => $productId] : $productId;
            $productCollection = $this->productCollectionFactory->create()->addFieldToFilter('entity_id', $condition);
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->productFactory->create(),
                ]
            );
        }

        if ($this->productId && $isArray) {
            return $this->productId;
        } elseif ($this->productId) {
            return true;
        }

        return false;
    }

    /**
     * @param string $args
     *
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        if ($this->getConditions()->validate($product)) {
            $this->productId[] = $product->getId();
        }
    }

    /**
     * @param string $categoryId
     * @return bool
     */
    public function isCategoryApplied($categoryId)
    {
        if ($this->categoryId === null) {
            $this->categoryId = [];
            $this->setCollectedAttributes([]);
            $categoryCollection = $this->categoryCollectionFactory->create()->addFieldToFilter(
                'entity_id',
                $categoryId
            );
            $this->getConditions()->collectValidatedAttributes($categoryCollection);

            $this->resourceIterator->walk(
                $categoryCollection->getSelect(),
                [[$this, 'callbackValidateCategory']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'category' => $this->categoryFactory->create(),
                ]
            );
        }

        if ($this->categoryId) {
            return true;
        }

        return false;
    }

    /**
     * @param string $args
     *
     * @return void
     */
    public function callbackValidateCategory($args)
    {
        $category = clone $args['category'];
        $category->setData($args['row']);
        if ($this->getConditions()->validate($category)) {
            $this->categoryId[] = $category->getId();
        }
    }

    /**
     * Retrieve rule combine conditions model
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditions()
    {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }

        // Load rule conditions if it is applicable
        if ($this->hasConditionsSerialized()) {
            $conditions = $this->getConditionsSerialized();
            if (!empty($conditions)) {
                $decode = json_decode($conditions);
                if ($decode) { //M2.2 compatibility
                    $conditions = $this->serializer->unserialize($conditions);
                } else {
                    $conditions = unserialize($conditions);
                }
                if (is_array($conditions) && !empty($conditions)) {
                    $this->_conditions->loadArray($conditions);
                }
            }
            $this->unsConditionsSerialized();
        }

        return $this->_conditions;
    }
}
