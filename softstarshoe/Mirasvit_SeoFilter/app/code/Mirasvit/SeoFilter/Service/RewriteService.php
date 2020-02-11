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


namespace Mirasvit\SeoFilter\Service;

use Mirasvit\SeoFilter\Api\Service\RewriteServiceInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as EntityAttributeCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as EntityAttributeOptionCollectionFactory;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Mirasvit\SeoFilter\Api\Service\FilterLabelServiceInterface;

class RewriteService implements RewriteServiceInterface
{
    protected static $activeFilters = null;

    /**
     * @param StoreManagerInterface $storeManager
     * @param EntityAttributeCollectionFactory $entityAttributeCollection
     * @param EntityAttributeOptionCollectionFactory $attributeOptionCollection
     * @param RewriteRepositoryInterface $rewriteRepository
     * @param LayerResolver $layerResolver
     * @param FilterLabelServiceInterface $filterLabelService
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EntityAttributeCollectionFactory $entityAttributeCollection,
        EntityAttributeOptionCollectionFactory $attributeOptionCollection,
        RewriteRepositoryInterface $rewriteRepository,
        LayerResolver $layerResolver,
        FilterLabelServiceInterface $filterLabelService
    ) {
        $this->storeManager = $storeManager;
        $this->storeId = $this->storeManager->getStore()->getId();
        $this->entityAttributeCollection = $entityAttributeCollection;
        $this->attributeOptionCollection = $attributeOptionCollection;
        $this->rewriteRepository = $rewriteRepository;
        $this->layerResolver = $layerResolver;
        $this->filterLabelService = $filterLabelService;
    }

    /**
     * {@inheritdoc}
     */
    public function getRewriteForFilterOption($attributeCode, $attributeId, $optionId) {
        $rewrite = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE, $attributeCode)
            ->addFieldToFilter(RewriteInterface::OPTION_ID, $optionId)
            ->addFieldToFilter(RewriteInterface::STORE_ID, $this->storeId)
            ->getFirstItem();

        if ($rewrite && is_object($rewrite) && $rewrite->getId()) {
            return $rewrite->getRewrite();
        }

        $rewrite = $this->generateNewRewrite($attributeCode, $attributeId, $optionId);

        return $rewrite;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveFilters() {

        if (self::$activeFilters === null) {
            $layer = $this->layerResolver->get();
            $activeFilters = $layer->getState()->getFilters();
            foreach ($activeFilters as $item) {
                $optionId = $item->getValue();
                $filter = $item->getFilter();
                $attributeId =  $filter->getAttributeModel()->getAttributeId();
                $attributeCode = $filter->getAttributeModel()->getAttributeCode();
                self::$activeFilters[$attributeCode] = $this->getRewriteForFilterOption($attributeCode, $attributeId, $optionId);
            }
        }

        return (self::$activeFilters === null) ? [] : self::$activeFilters;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNewRewrite($attributeCode, $attributeId, $optionId)
    {
        if (!(int) $optionId && $attributeCode != RewriteInterface::PRICE) {
            return false;
        }

        $item = $this->attributeOptionCollection
            ->create()
            ->setStoreFilter($this->storeId, true)
            ->setIdFilter($optionId)
            ->setAttributeFilter($attributeId)
            ->getFirstItem();

        $entityAttributeCollection = $this->entityAttributeCollection->create()
            ->addFieldToFilter('attribute_id', $attributeId);
        $option = $entityAttributeCollection->getFirstItem();

        if (($option->getAttributeId() !=  $item->getAttributeId()
            && $attributeCode != RewriteInterface::PRICE)
            || $option->getAttributeCode() != $attributeCode) {
                return false;
        }

        $label = $this->filterLabelService->getLabel($attributeCode, $optionId, $item->getValue());

        $rewrite = $this->rewriteRepository->create();
        $rewrite->setAttributeCode($attributeCode)
            ->setOptionId((int)$optionId)
            ->setRewrite($label)
            ->setStoreId($this->storeId);

        if ($attributeCode == RewriteInterface::PRICE) {
            $rewrite->setPriceOptionId($optionId);
        }

        $this->rewriteRepository->save($rewrite);

        return $label;
    }
}