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


namespace Mirasvit\Seo\Service\FriendlyUrl;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewrite;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\Product\Visibility;
use \Mirasvit\Seo\Api\Service\FriendlyUrl\ProductUrlKeyTemplateInterface as UrlKey;
use \Mirasvit\Seo\Api\Data\SuffixInterface as Suffix;
use \Mirasvit\Seo\Api\Data\TableInterface as Table;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductUrlKeyTemplate implements \Mirasvit\Seo\Api\Service\FriendlyUrl\ProductUrlKeyTemplateInterface
{
    /**
     * @var Suffix
     */
    protected $producSuffix;

    /**
     * @var Table
     */
    protected $tableName;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface
     */
    protected $productUrlTemplateConfig;

    /**
     * @var \Magento\UrlRewrite\Model\UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var  \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * @var Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    protected $connection;

    /**
     * @param Suffix $producSuffix,
     * @param Table $tableName,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface $productUrlTemplateConfig,
     * @param \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist,
     * @param \Magento\Framework\App\ResourceConnection $resource,
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    public function __construct(
        Suffix $producSuffix,
        Table $tableName,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface $productUrlTemplateConfig,
        \Magento\UrlRewrite\Model\UrlPersistInterface $urlPersist,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        $this->producSuffix = $producSuffix;
        $this->tableName = $tableName;
        $this->storeManager = $storeManager;
        $this->productUrlTemplateConfig = $productUrlTemplateConfig;
        $this->urlPersist = $urlPersist;
        $this->resource = $resource;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * @return bool|array
     */
    public function getUrlKeyTemplate() 
    {
        $urlTemplate = [];
        $isUrlKeyTemplateEnabled = false;
        foreach ($this->storeManager->getStores() as $store) {
            $productUrlKey = $this->productUrlTemplateConfig->getProductUrlKey($store->getId());
            if ($store->getIsActive() && $productUrlKey && strpos($productUrlKey, '[') !== false) {
                $urlTemplate[$store->getId()] = $productUrlKey;
                $isUrlKeyTemplateEnabled = true;
            } else {
                $urlTemplate[$store->getId()] = false;
            }
        }

        if ($isUrlKeyTemplateEnabled) {
            return $urlTemplate;
        }

        return $isUrlKeyTemplateEnabled;
    }

    /**
     * @param string $urlKey
     * @param int    $productId
     * @param int    $storeId
     *
     * @return bool|array
     */
    public function checkUrlKeyUnique($urlKey, $productId, $storeId) 
    {
        $isUniqueUrlKey = true;
        $url = $urlKey;
        if ($suffix = $this->producSuffix->getProductUrlSuffix($storeId)) {
            $url = $urlKey . $suffix;
        }

        $this->connection = $this->resource->getConnection();
        $table = $this->tableName->getTable('url_rewrite');

        $select = $this->connection->select()
            ->from($table, '*')
            ->where("'" . UrlKey::ENTITY_TYPE . "'" . ' = entity_type')
            ->where("$storeId = store_id")
            ->where("'" . $url . "'" . '= request_path')
            ->where('entity_id != ?', $productId);

        $selectData = $this->connection->fetchAll($select);

        if ($selectData) {
            $isUniqueUrlKey = $selectData;
        }

        return $isUniqueUrlKey;
    }

    /**
     * @param string $urlKey
     * @param object $product
     *
     * @return void
     */
    public function applyUrlKey($urlKey, $product) 
    {
        $product->setUrlKey($urlKey);

        $this->urlPersist->deleteByData([
            UrlRewrite::ENTITY_ID => $product->getId(),
            UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::REDIRECT_TYPE => 0,
            UrlRewrite::STORE_ID => $product->getStoreId()
        ]);

        if ($product->isVisibleInSiteVisibility()) {
            //setup::install compatibility
            $productUrlRewriteGenerator = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator::class
            );
            $this->urlPersist->replace($productUrlRewriteGenerator->generate($product));
            $this->updateEntityUrlKey($urlKey, $product);
        }
    }

    /**
     * @param string $urlKey
     * @param object $product
     *
     * @return void
     */
    public function updateEntityUrlKey($urlKey, $product) 
    {
        $productId = $product->getId();
        $attributeId = $this->eavAttribute->getIdByCode('catalog_product', 'url_key');
        $storeId = $product->getStoreId();
        $table = $this->tableName->getTable('catalog_product_entity_varchar');
        $select = $this->connection->select()
                    ->from($table, '*')
                    ->where("$attributeId = attribute_id")
                    ->where("$storeId = store_id")
                    ->where("$productId = entity_id");

        $row = $this->connection->fetchRow($select);

        if ($row) {
            $bind = ['value' => $urlKey];
            $where = ['value_id = ?' => $row['value_id']];
            $this->connection->update($table, $bind, $where);
        } else {
            $bind = [
                'attribute_id' => $attributeId,
                'store_id' => $storeId,
                'entity_id' => $product->getId(),
                'value' => $urlKey,
            ];

            $this->connection->insert($table, $bind);
        }
    }
}

