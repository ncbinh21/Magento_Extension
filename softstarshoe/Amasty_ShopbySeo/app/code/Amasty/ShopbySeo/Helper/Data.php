<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbySeo\Helper;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Model\Cache\Type;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Framework\App\Cache;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManager;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory;
use Amasty\Shopby\Helper\Group;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as OptionSettingCollectionFactory;

class Data extends AbstractHelper
{
    const CANONICAL_ROOT = 'amasty_shopby_seo/canonical/root';
    const CANONICAL_CATEGORY = 'amasty_shopby_seo/canonical/category';

    /**
     * @var CollectionFactory
     */
    protected $settingCollectionFactory;
    /**
     * @var Option\CollectionFactory
     */
    protected $optionCollectionFactory;
    /**
     * @var  OptionSettingCollectionFactory
     */
    protected $optionSettingCollectionFactory;
    /**
     * @var  StoreManager
     */
    protected $storeManager;

    /**
     * @var  \Magento\Catalog\Model\Product\Url
     */
    protected $productUrl;

    /**
     * @var  Type
     */
    protected $cache;

    /**
     * @var  Cache\StateInterface
     */
    protected $cacheState;

    /**
     * @var Group
     */
    protected $groupHelper;

    protected $seoSignificantUrlParameters;

    protected $optionsSeoData;

    public function __construct(
        Context $context,
        CollectionFactory $settingCollectionFactory,
        Option\CollectionFactory $optionCollectionFactory,
        \Magento\Catalog\Model\Product\Url $productUrl,
        OptionSettingCollectionFactory $optionSettingCollectionFactory,
        StoreManager $storeManager,
        Cache $cache,
        Group $groupHelper,
        Cache\StateInterface $cacheState
    ) {
        parent::__construct($context);
        $this->settingCollectionFactory = $settingCollectionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->optionSettingCollectionFactory = $optionSettingCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productUrl = $productUrl;
        $this->cache = $cache;
        $this->cacheState = $cacheState;
        $this->groupHelper = $groupHelper;
    }

    public function getSeoSignificantUrlParameters()
    {
        if ($this->seoSignificantUrlParameters === null) {
            $this->seoSignificantUrlParameters = $this->getSeoSignificantAttributeCodes();
        }
        return $this->seoSignificantUrlParameters;
    }

    public function getOptionsSeoData()
    {
        $cache_id = 'amshopby_seo_options_data' . $this->storeManager->getStore()->getId();
        if ($this->optionsSeoData === null && $this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
            $cached = $this->cache->load($cache_id);
            if ($cached !== false) {
                $this->optionsSeoData = unserialize($cached);
            }
        }
        if ($this->optionsSeoData === null) {
            $this->optionsSeoData = [];
            $aliasHash = [];

            $hardcodedAliases = $this->loadHardcodedAliases();
            foreach ($hardcodedAliases as $row) {
                $alias = $this->buildUniqueAlias($row['url_alias'], $aliasHash);
                if (strpos($row['filter_code'], 'attr_') === 0) {
                    $attributeCode = substr($row['filter_code'], strlen('attr_'));
                } else {
                    $attributeCode = '';
                }

                $this->optionsSeoData[$row['value']] = [
                    'alias' => $alias,
                    'attribute_code' => $attributeCode,
                ];
                $aliasHash[$alias] = $row['value'];
            }
            $dynamicAliases = $this->loadDynamicAliasesExcluding(array_values($aliasHash));
            $ids = [];
            foreach ($dynamicAliases as $row) {
                if (!array_key_exists($row['attribute_id'], $ids)) {
                    $ids[$row['attribute_id']] = $row['attribute_code'];
                }

                $alias = $this->buildUniqueAlias($row['value'], $aliasHash);
                $optionId = $row['option_id'];
                $this->optionsSeoData[$optionId] = [
                    'alias' => $alias,
                    'attribute_code' => $row['attribute_code'],
                ];
                $aliasHash[$alias] = $optionId;
            }
            foreach ($ids as $id => $code) {
                $data = $this->groupHelper->getAliasGroup($id);
                if ($data) {
                    foreach ($data as $key => $record) {
                        $this->optionsSeoData[$key] = [
                            'alias' => $record,
                            'attribute_code' => $code,
                        ];
                        $aliasHash[$record] = $key;
                    }
                }
            }
            if ($this->cacheState->isEnabled(Type::TYPE_IDENTIFIER)) {
                $this->cache->save(serialize($this->optionsSeoData), $cache_id, [Type::CACHE_TAG]);
            }
        }

        return $this->optionsSeoData;
    }

    protected function loadHardcodedAliases()
    {
        $optionSettingCollection = $this->optionSettingCollectionFactory->create();
        $select = $optionSettingCollection->getSelect();
        $storeId = intval($this->storeManager->getStore()->getId());

        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        if ($storeId === 0) {
            $select->columns('url_alias');
            $select->columns('filter_code');
            $select->columns('value');
            $select->where('`url_alias` <> ""');
            $select->where('`store_id` = ' . $storeId);
        } else {
            $mainTable = $optionSettingCollection->getMainTable();
            $subSelect = "SELECT `value` FROM `$mainTable` WHERE `url_alias` <> '' AND `store_id` = $storeId";
            $select->joinLeft(
                ['default_table' => $mainTable],
                "`default_table`.`value` = `main_table`.`value` AND `default_table`.`value` NOT IN ($subSelect)",
                [
                    'url_alias' =>
                        'IF(ISNULL(`default_table`.`url_alias`),
                        `main_table`.`url_alias`, `default_table`.`url_alias`)',
                    'filter_code' =>
                        'IF(ISNULL(`default_table`.`filter_code`),
                        `main_table`.`filter_code`, `default_table`.`filter_code`)',
                    'value' => 'IF(ISNULL(`default_table`.`value`), `main_table`.`value`, `default_table`.`value`)'
                ]
            );
            $select->where("`default_table`.`store_id` = 0 OR `main_table`.`store_id` = $storeId");
            $select->where(
                'IF(ISNULL(`default_table`.`url_alias`),
                `main_table`.`url_alias`, `default_table`.`url_alias`)  <> ""'
            );
        }
        $data = $select->getConnection()->fetchAll($select);
        return $data;
    }

    protected function loadDynamicAliasesExcluding($excludeOptionIds = [])
    {
        $seoAttributeCodes = $this->getSeoSignificantAttributeCodes();

        $collection = $this->optionCollectionFactory->create();
        $collection->join(['a' => 'eav_attribute'], 'a.attribute_id = main_table.attribute_id', ['attribute_code']);
        $collection->addFieldToFilter('attribute_code', ['in' => $seoAttributeCodes]);
        $collection->setStoreFilter();
        $select = $collection->getSelect();
        if ($excludeOptionIds) {
            $select->where('`main_table`.`option_id` NOT IN (' . join(',', $excludeOptionIds) . ')');
        }
        $statement = $select->query();
        $rows = $statement->fetchAll();
        return $rows;
    }

    protected function getSeoSignificantAttributeCodes()
    {
        $collection = $this->settingCollectionFactory->create();
        $collection->addFieldToFilter(FilterSettingInterface::IS_SEO_SIGNIFICANT, 1);
        $filterCodes = $collection->getColumnValues(FilterSettingInterface::FILTER_CODE);
        array_walk($filterCodes, function (&$code) {
            if (substr($code, 0, 5) == \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX) {
                $code = substr($code, 5);
            }
        });
        return $filterCodes;
    }

    protected function buildUniqueAlias($value, $hash)
    {
        if (preg_match('@^[\d\.]+$@s', $value)) {
            $format = $value;
        } else {
            $format = $this->productUrl->formatUrlKey($value);
        }
        if ($format == '') {
            // Magento formats '-' as ''
            $format = '-';
        }

        $unique = $format;
        for ($i=1; array_key_exists($unique, $hash); $i++) {
            $unique = $format . '-' . $i;
        }
        return $unique;
    }

    public function getCanonicalRoot()
    {
        return $this->scopeConfig->getValue(self::CANONICAL_ROOT, ScopeInterface::SCOPE_STORE);
    }

    public function getCanonicalCategory()
    {
        return $this->scopeConfig->getValue(self::CANONICAL_CATEGORY, ScopeInterface::SCOPE_STORE);
    }

    public function getGeneralUrl()
    {
        return $this->scopeConfig->getValue('amshopby_root/general/url', ScopeInterface::SCOPE_STORE);
    }

    public function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE);
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }
}
