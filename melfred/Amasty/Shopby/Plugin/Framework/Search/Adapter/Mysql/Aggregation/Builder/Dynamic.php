<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Framework\Search\Request\Aggregation\DynamicBucket;

class Dynamic
{
    const FROM_TO_WIDGET = '1';
    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $filterSettingHelper;

    /**
     * @var Collection
     */
    protected $dataProvider;

    /**
     * @var \Magento\Framework\Search\Dynamic\EntityStorageFactory
     */
    protected $entityStorageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    protected $groupHelper;

    /**
     * @var array
     */
    private $data = [];

    /**
     * DynamicAggregation constructor.
     * @param ResourceConnection $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper
     * @param \Amasty\Shopby\Helper\Group $groupHelper
     * @param \Magento\Framework\Search\Dynamic\DataProviderInterface $priceDataProvider
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Search\Dynamic\EntityStorageFactory $entityStorageFactory
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        \Magento\Eav\Model\Config $eavConfig,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        \Amasty\Shopby\Helper\Group $groupHelper,
        \Magento\Framework\Search\Dynamic\DataProviderInterface $priceDataProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Search\Dynamic\EntityStorageFactory $entityStorageFactory
    ) {
        $this->resource = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->eavConfig = $eavConfig;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->groupHelper = $groupHelper;
        $this->priceDataProvider = $priceDataProvider;
        $this->entityStorageFactory = $entityStorageFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Dynamic $subject
     * @param \Closure $closure
     * @param \Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface $dataProvider
     * @param array $dimensions
     * @param \Magento\Framework\Search\Request\BucketInterface $bucket
     * @param \Magento\Framework\DB\Ddl\Table $entityIdsTable
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundBuild(
        \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Dynamic $subject,
        \Closure $closure,
        \Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface $dataProvider,
        array $dimensions,
        \Magento\Framework\Search\Request\BucketInterface $bucket,
        \Magento\Framework\DB\Ddl\Table $entityIdsTable
    ) {
        $dataKey = $bucket->getName() . $bucket->getField() . $bucket->getType();
        if (!isset($this->data[$dataKey])) {
            $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $bucket->getField());

            if ($attribute->getBackendType() == 'decimal') {
                $options = $this->groupHelper->getAttributeGroupsValues($attribute->getAttributeId());
                $groups = [];
                if (count($options)) {
                    $groups = $this->groupHelper->getMinMax($options);
                    if (count($groups)) {
                        $newBucket = new DynamicBucket($bucket->getName(), $bucket->getField(), 'group_manual');
                        $bucket = $newBucket;
                        $dimensions['group'] = $groups;
                    }
                }

                $filterSetting = $this->filterSettingHelper->getSettingByAttribute($attribute);
                if ($filterSetting->getDisplayMode() == \Amasty\Shopby\Model\Source\DisplayMode::MODE_SLIDER ||
                    $filterSetting->getDisplayMode() == \Amasty\Shopby\Model\Source\DisplayMode::MODE_FROM_TO_ONLY ||
                    $filterSetting->getAddFromToWidget() === self::FROM_TO_WIDGET
                ) {
                    $rangeCalculationPath = $this->scopeConfig->getValue(
                        AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    if ($attribute->getAttributeCode() == 'price') {
                        if ($rangeCalculationPath == AlgorithmFactory::RANGE_CALCULATION_IMPROVED
                            && !count($groups)
                        ) {
                            $bucket = new DynamicBucket($bucket->getName(), $bucket->getField(), 'auto');
                        }
                        $minMaxData['data'] = $this->priceDataProvider->getAggregations(
                            $this->entityStorageFactory->create($entityIdsTable)
                        );
                        $minMaxData['data']['value'] = 'data';
                    } else {
                        $currentScope = $dimensions['scope']->getValue();
                        $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();
                        $select = $this->resource->getConnection()->select();
                        $table = $this->resource->getTableName(
                            'catalog_product_index_eav_decimal'
                        );
                        $select->from(['main_table' => $table], ['value'])
                            ->where('main_table.attribute_id = ?', $attribute->getAttributeId())
                            ->where('main_table.store_id = ? ', $currentScopeId);
                        $select->joinInner(
                            ['entities' => $entityIdsTable->getName()],
                            'main_table.entity_id  = entities.entity_id',
                            []
                        );
                        /** @var Select $fullQuery */
                        $fullQuery = $this->resource->getConnection()
                            ->select();

                        $fullQuery->from(['main_table' => $select], ['value' => new \Zend_Db_Expr("'data'")]);
                        $fullQuery->columns(
                            ['min' => 'min(main_table.value)',
                                'max' => 'max(main_table.value)',
                                'count' => 'count(*)'
                            ]
                        );

                        $minMaxData = $dataProvider->execute($fullQuery);
                    }

                    $defaultData = $closure($dataProvider, $dimensions, $bucket, $entityIdsTable);

                    return array_replace($minMaxData, $defaultData);
                }
            }

            $this->data[$dataKey] = $closure($dataProvider, $dimensions, $bucket, $entityIdsTable);
        }

        return $this->data[$dataKey];
    }
}
