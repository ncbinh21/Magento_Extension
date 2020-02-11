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



namespace Mirasvit\Seo\Model\System\Template;

class Worker extends \Magento\Framework\DataObject
{
    /**
     * @var \Mirasvit\Seo\Model\SeoObject\ProducturlFactory
     */
    protected $objectProducturlFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Helper\Product\Url
     */
    protected $catalogProductUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $dbResource;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Seo\Model\SeoObject\ProducturlFactory                $objectProducturlFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Mirasvit\Seo\Model\Config                                     $config
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface             $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection                      $dbResource
     * @param \Magento\Framework\Model\Context                               $context
     * @param array                                                          $data
     */
    public function __construct(
        \Mirasvit\Seo\Model\SeoObject\ProducturlFactory $objectProducturlFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Mirasvit\Seo\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $dbResource,
        \Magento\Framework\Model\Context $context,
        array $data = []
    ) {
        $this->objectProducturlFactory = $objectProducturlFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;

        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->dbResource = $dbResource;
        $this->context = $context;
        parent::__construct($data);
    }

    /**
     * @var int
     */
    protected $maxPerStep = 500;
    /**
     * @var int
     */
    protected $totalNumber;
    /**
     * @var bool
     */
    protected $isEnterprise = false;

    /**
     * @return bool
     */
    public function run()
    {
        $this->totalNumber = $this->getTotalProductNumber();
        if (($this->getStep() - 1) * $this->maxPerStep >= $this->totalNumber) {
            return false;
        }
        $this->process();

        return true;
    }

    /**
     * @return string
     */
    protected function getTotalProductNumber()
    {
        $connection = $this->dbResource->getConnection('core_write');
        $select = $connection->select()->from($this->dbResource->getTableName('catalog_product_entity'));
        $select->reset(\Zend_Db_Select::ORDER);
        $select->reset(\Zend_Db_Select::LIMIT_COUNT);
        $select->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(\Zend_Db_Select::COLUMNS);
        $select->columns('COUNT(*)');
        $number = $connection->fetchOne($select);

        return $number;
    }

    /**
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $this->catalogProductUrl->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * @return int
     */
    public function getMaxPerStep()
    {
        return $this->maxPerStep;
    }

    /**
     * @return int
     */
    public function getCurrentNumber()
    {
        $c = $this->getStep() * $this->getMaxPerStep();
        if ($c > $this->totalNumber) {
            return $this->totalNumber;
        } else {
            return $c;
        }
    }

    /**
     * @return int
     */
    public function getTotalNumber()
    {
        return $this->totalNumber;
    }

    /**
     * @param string $connection
     * @param string $urlKey
     * @param string $urlKeyTable
     * @return bool|string
     */
    public function prepareUrlKeys($connection, $urlKey, $urlKeyTable)
    {
        //for Magento Enterprise only
        if ($urlKey) {
            $selectAllStores = $connection->select()->from($urlKeyTable)->
                                    where('value LIKE ?', $urlKey.'%');
            $rowAllStores = $connection->fetchAll($selectAllStores);
            if ($rowAllStores) {
                $urlKeyValues = [];
                $addNewKey = false;
                foreach ($rowAllStores as $valueStores) {
                    if ($valueStores['value'] == $urlKey) {
                        $addNewKey = true;
                    }
                    $urlKeyValues[] = $valueStores['value'];
                }
                if ($addNewKey) {
                    //True if such url key exist. We can't add the same url key because value is UNIQUE
                    // for magento Enterprise.
                    $i = 1;
                    do {
                        $urlNewKey = $urlKey.'-'.$i;
                        ++$i;
                    } while (in_array($urlNewKey, $urlKeyValues));
                    $urlKey = $urlNewKey;

                    return $urlKey;
                }
            }
        }

        return false;
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function process()
    {
        $connection = $this->dbResource->getConnection('core_write');

        $select = $connection
            ->select()
            ->from($this->dbResource->getTableName('eav_entity_type'))
            ->where("entity_type_code = 'catalog_product'");
        $productTypeId = $connection->fetchOne($select);
        $select = $connection
            ->select()
            ->from($this->dbResource->getTableName('eav_attribute'))
            ->where("entity_type_id = $productTypeId AND (attribute_code = 'url_path')");
        $urlPathId = $connection->fetchOne($select);
        $select = $connection
            ->select()
            ->from($this->dbResource->getTableName('eav_attribute'))
            ->where("entity_type_id = $productTypeId AND (attribute_code = 'url_key')");
        $urlKeyId = $connection->fetchOne($select);

        $config = $this->config;
        $stores = $this->storeManager->getStores();
        $urlKeyTable = $this->dbResource->getTableName('catalog_product_entity_varchar');
        foreach ($stores as $store) {
            $products = $this->productCollectionFactory->create()
                        ->addAttributeToSelect('*')
                        ->setCurPage($this->getStep())
                        ->setPageSize($this->maxPerStep)
                        ->setStore($store);
            foreach ($products as $product) {
                $urlKeyTemplate = $config->getProductUrlKey($store);
                if ($this->isEnterprise) {
                    if (empty($urlKeyTemplate)) {
                        // if "Product URL Key Template" is empty we will create [product_name] template
                        $urlKeyTemplate = '[product_name]';
                    }
                }
                $storeId = $store->getId();
                $templ = $this->objectProducturlFactory->create()
                            ->setProduct($product)
                            ->setStore($store);
                $urlKey = $templ->parse($urlKeyTemplate);
                $urlKey = $this->formatUrlKey($urlKey);

                if ($product->getUrlKey() == $urlKey) {
                    continue;
                }

                $urlSuffix = $this->scopeConfig->getValue(
                    'catalog/seo/product_url_suffix',
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    $store
                );

                $select = $connection->select()->from($urlKeyTable)->
                            where("entity_type_id = $productTypeId AND attribute_id = $urlKeyId AND entity_id = {
                                $product->getId()
                            } AND store_id = {$storeId}");

                $row = $connection->fetchRow($select); //echo $select;die;
                if ($row) {
                    if ($this->isEnterprise) {
                        if ($urlKeyPrepared = $this->prepareUrlKeys($connection, $urlKey, $urlKeyTable)) {
                            $urlKey = $urlKeyPrepared;
                        }
                    }

                    $connection->update(
                        $urlKeyTable,
                        [
                        'value' => $urlKey],
                        "entity_type_id = $productTypeId AND attribute_id = $urlKeyId AND entity_id = {
                            $product->getId()
                        } AND store_id = {$storeId}"
                    );
                } else {
                    if (!$this->isEnterprise) {
                        $data = [
                            'entity_type_id' => $productTypeId,
                            'attribute_id' => $urlKeyId,
                            'entity_id' => $product->getId(),
                            'store_id' => $storeId,
                            'value' => $urlKey,
                        ];

                        $connection->insert($urlKeyTable, $data);
                    }
                }

                if (!$this->isEnterprise) {
                    $select = $connection->select()->from($urlKeyTable)->
                            where("entity_type_id = $productTypeId AND attribute_id = $urlPathId AND entity_id = {
                            $product->getId()
                            } AND store_id = {$storeId}");
                    $row = $connection->fetchRow($select);
                    if ($row) {
                        $connection->update(
                            $urlKeyTable,
                            [
                            'value' => $urlKey.$urlSuffix],
                            "entity_type_id = $productTypeId AND attribute_id = $urlPathId AND entity_id = {
                                $product->getId()
                                } AND store_id = {$storeId}"
                        );
                    } else {
                        $data = [
                            'entity_type_id' => $productTypeId,
                            'attribute_id' => $urlPathId,
                            'entity_id' => $product->getId(),
                            'store_id' => $storeId,
                            'value' => $urlKey.$urlSuffix,
                        ];
                        $connection->insert($urlKeyTable, $data);
                    }
                }
            }
        }
    }
}
