<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Model\Rule;

use Forix\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Forix\ProductLabel\Model\ResourceModel\Rule\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Registry;
use Forix\ProductLabel\Model\Rule;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\SalesRule\Model\Rule\Metadata\ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * Initialize dependencies.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->coreRegistry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var \Forix\ProductLabel\Model\Rule $rule */
        $rule = $this->coreRegistry->registry('current_label_rule');
        if ($rule->getId()) {
            $ruleData = $rule->getData();
            if ($rule->getCategoryImage()) {
                unset($ruleData['category_image']);
                $ruleData['category_image'][0]['name'] = $rule->getData('category_image');
                $ruleData['category_image'][0]['url'] = $rule->getCategoryImageUrl();
            }

            if ($rule->getProductImage()) {
                unset($ruleData['product_image']);
                $ruleData['product_image'][0]['name'] = $rule->getData('product_image');
                $ruleData['product_image'][0]['url'] = $rule->getProductImageUrl();
            }

            $this->loadedData[$rule->getId()] = $ruleData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
