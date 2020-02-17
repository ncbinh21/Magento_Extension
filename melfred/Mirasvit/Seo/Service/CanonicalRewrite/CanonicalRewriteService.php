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



namespace Mirasvit\Seo\Service\CanonicalRewrite;

use Mirasvit\Seo\Api\Service\CanonicalRewrite\CanonicalRewriteServiceInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;

class CanonicalRewriteService implements CanonicalRewriteServiceInterface
{
    /**
     * @var int
     */
    protected $productId;
    /**
     * @var int
     */
    protected $categoryId;

    public function __construct(
        \Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface $canonicalRewriteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->canonicalRewriteRepository = $canonicalRewriteRepository;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->resourceIterator = $resourceIterator;
        $this->urlInterface = $urlInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalRewriteRule()
    {
        $productId = false;
        $categoryId = false;
        $canonicalRewriteRule = false;

        if ($this->registry->registry('current_product')) {
            $productId = $this->registry->registry('current_product')->getId();
        } elseif ($this->registry->registry('current_category')) {
            $categoryId = $this->registry->registry('current_category')->getId();
        }

        if (!$productId && !$categoryId) {
            return false;
        }

        $collection = $this->canonicalRewriteRepository->getCollection()
            ->addStoreFilter($this->storeManager->getStore())
            ->addActiveFilter()
            ->addSortOrder();

        $uri = $this->urlInterface->getCurrentUrl();

        foreach ($collection as $item) {
            $expression = $item->getRegExpression();
            if (@preg_match($expression, $uri)) {
                $canonicalRewriteRule = $item;
                break;
            }
            if ($this->getElementApplied($productId, $categoryId, $item)) {
                $canonicalRewriteRule = $item;
                break;
            }
        }

        return $canonicalRewriteRule;
    }

    /**
     * @param string $productId
     * @param string $categoryId
     * @param Mirasvit\Seo\Model\CanonicalRewrite $item
     * @return bool
     */
    public function getElementApplied(
        $productId,
        $categoryId,
        $item
    ) {
        $isElementApplied = false;
        if ($productId) {
            $rule = $this->getRuleById($item->getId());
            $isElementApplied = $this->isProductApplied($productId, $rule);
        } elseif ($categoryId) {
            $rule = $this->getRuleById($item->getId());
            $isElementApplied = $this->isCategoryApplied($categoryId, $rule);
        }

        return $isElementApplied;
    }

    /**
     * @param int $ruleId
     * @return \Mirasvit\Seo\Model\CanonicalRewrite
     */
    public function getRuleById($ruleId)
    {
        $rule = $this->canonicalRewriteRepository->getCollection()
            ->addFieldToFilter(CanonicalRewriteInterface::ID, $ruleId)
            ->getFirstItem();
        $rule = $rule->load($rule->getId());

        return $rule;
    }

    /**
     * @param string $productId
     * @param Mirasvit\Seo\Model\CanonicalRewrite $rule
     * @return bool
     */
    public function isProductApplied($productId, $rule)
    {
        if ($this->productId === null) {
            $this->productIds = [];
            $rule->setCollectedAttributes([]);
            $productCollection = $this->productCollectionFactory->create()->addFieldToFilter(
                'entity_id',
                $productId
            );
            $rule->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $rule->getCollectedAttributes(),
                    'product' => $this->productFactory->create(),
                    'rule' => $rule,
                ]
            );
        }

        if ($this->productId) {
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
        if ($args['rule']->getConditions()->validate($product)) {
            $this->productId[] = $product->getId();
        }
    }

    /**
     * @param string $categoryId
     * @param Mirasvit\Seo\Model\CanonicalRewrite $rule
     * @return bool
     */
    public function isCategoryApplied($categoryId, $rule)
    {
        if ($this->categoryId === null) {
            $this->categoryId = [];
            $rule->setCollectedAttributes([]);
            $categoryCollection = $this->categoryCollectionFactory->create()->addFieldToFilter(
                'entity_id',
                $categoryId
            );
            $rule->getConditions()->collectValidatedAttributes($categoryCollection);

            $this->resourceIterator->walk(
                $categoryCollection->getSelect(),
                [[$this, 'callbackValidateCategory']],
                [
                    'attributes' => $rule->getCollectedAttributes(),
                    'category' => $this->categoryFactory->create(),
                    'rule' => $rule,
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
        if ($args['rule']->getConditions()->validate($category)) {
            $this->categoryId[] = $category->getId();
        }
    }
}