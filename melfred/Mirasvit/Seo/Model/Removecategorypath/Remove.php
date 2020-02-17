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



namespace Mirasvit\Seo\Model\Removecategorypath;

// use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
// use Magento\Store\Model\ScopeInterface;
// use Magento\Store\Model\StoreManagerInterface;

class Remove extends \Magento\Framework\DataObject
{
    /**
     * Rewrite type
     */
    const ENTITY_TYPE = 'category';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection
     */
    protected $urlRewriteCategory;

    /**
     * @var  \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    protected $connection;

    /**
     * url_rewrite table name
     *
     * @var string
     */
    protected $table;

    /**
     * Request path of processed category
     */
    protected $requestPath;

    /**
     * Id of processed category
     *
     * @var int
     */
    protected $categoryId;

    /**
     * Store Id of processed category
     *
     * @var int
     */
    protected $storeId;

    /**
     * Changed url path of processed category with suffix
     *
     * @var int
     */
    protected $oldUrlPath;

    /**
     * Url path of processed category with suffix
     *
     * @var int
     */
    protected $preparedUrlPath;

    /**
     * Store Ids for which will remove parent category path
     *
     * @var int
     */
    protected $storeIds;

    /**
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite
     * @param \Magento\Framework\App\ResourceConnection                    $resource
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->registry = $registry;
        $this->urlRewriteCategory = $urlRewrite->addFieldToFilter('entity_type', 'category')
                                                ->addFieldToFilter('redirect_type', 0);
        $this->resource = $resource;
    }

    /**
     * Remove Parent Category Path for Category URLs
     *
     * @return string
     */
    public function removeParentCategoryPath()
    {
        $this->storeIds = $this->registry->registry('store_ids_for_remove_parent_category_path');
        if(!$this->storeIds) {
            return 'Something went wrong.';
        }

        $this->connection = $this->resource->getConnection();
        $this->table = $this->getTable('url_rewrite');

        $urlRewriteCategory = $this->urlRewriteCategory->addFieldToFilter('store_id', ['in' => $this->storeIds]);

        foreach ($urlRewriteCategory as $categoryRewrite) {
            $this->categoryId = $categoryRewrite->getEntityId();
            $this->requestPath = $categoryRewrite->getRequestPath();
            $this->storeId = $categoryRewrite->getStoreId();

            if ($this->table
                && strpos($this->requestPath, '/') !== false
                && $this->debugForSpecifiedCategory() ) {
                    $update = $this->updateCategoryUrlRewrite();

                    if ($update) {
                        $this->insertCategoryUrlRewriteReirect();
                        $this->updateCategoryUrlRewriteRedirect();
                    }
            }
        }

        return 'Parent category path has been removed.';
    }

    /**
     * Debug only for a specified category
     *
     * @return bool
     */
    protected function debugForSpecifiedCategory()
    {
        $categoryId = null; //for debug set category id instead null
        if ($categoryId !== null && $this->categoryId == $categoryId ) {
            return true;
        } elseif ($categoryId !== null) {
            return false;
        }

        return true;
    }

    /**
     * Upadate data in url_rewrite table of processed category
     *
     * @return bool
     */
    protected function updateCategoryUrlRewrite()
    {
        $select = $this->connection->select()
            ->from($this->table, 'url_rewrite_id')
            ->where("'" . self::ENTITY_TYPE . "'" . ' = entity_type')
            ->where("$this->storeId = store_id")
            ->where("$this->categoryId = entity_id")
            ->where('0 = redirect_type');
        $bind = [
            'entity_type' => self::ENTITY_TYPE,
            'store_id' => $this->storeId,
            'entity_id' => $this->categoryId,
            'redirect_type' => 0,
        ];

        $urlRewriteId = $this->connection->fetchOne($select, $bind);

        if ($urlRewriteId) {
            $this->oldUrlPath = $this->requestPath;
            $this->preparedUrlPath = $this->getPrepareRequestPath();
            $this->deleteRedirect(); //delete redirect with the same request_path which we will insert

            $bind = ['request_path' => $this->preparedUrlPath];
            $where = ['url_rewrite_id = ?' => (int) $urlRewriteId];

            $this->connection->update($this->table, $bind, $where);

            return true;
        }

        return false;
    }

    /**
     * Remove parent category path
     *
     * @return bool
     */
    protected function getPrepareRequestPath()
    {
        $path = explode('/', $this->requestPath);
        $this->preparedRequestPath = $path[count($path) - 1];

        return $this->preparedRequestPath;
    }

    /**
     * Delete redirect if exist
     *
     * @return bool
     */
    protected function deleteRedirect()
    {
        $select = $this->connection->select()
            ->from($this->table, 'url_rewrite_id')
            ->where("'" . self::ENTITY_TYPE . "'" . ' = entity_type')
            ->where("$this->storeId = store_id")
            ->where("$this->categoryId = entity_id")
            ->where('0 < redirect_type')
            ->where("'" . $this->preparedUrlPath . "'" . '= request_path')
            ;
        $bind = [
            'entity_type' => self::ENTITY_TYPE,
            'store_id' => $this->storeId,
            'entity_id' => $this->categoryId,
            'redirect_type' => '3%',
            'request_path' => $this->preparedUrlPath,
        ];

        if ($urlRewriteId = $this->connection->fetchOne($select, $bind)) {
            $this->connection->delete($this->table, ['url_rewrite_id = ?' => (int) $urlRewriteId]);
        }

        return true;
    }

    /**
     * Insert data in url_rewrite table
     *
     * @return bool
     */
    protected function insertCategoryUrlRewriteReirect()
    {
        $bind = [
            'entity_type' => self::ENTITY_TYPE,
            'entity_id' => $this->categoryId,
            'request_path' => $this->oldUrlPath,
            'target_path' => $this->preparedUrlPath,
            'redirect_type' => '301',
            'store_id' => $this->storeId,
            'description' => null,
            'is_autogenerated' => 0,
            'metadata' => null,
        ];

        $this->connection->insert($this->table, $bind);

        return true;
    }

    /**
     * Upadate data in url_rewrite table for redirect
     *
     * @return $this
     */
    protected function updateCategoryUrlRewriteRedirect()
    {
        $select = $this->connection->select()
            ->from($this->table, 'url_rewrite_id')
            ->where("'" . self::ENTITY_TYPE . "'" . ' = entity_type')
            ->where("$this->storeId = store_id")
            ->where("$this->categoryId = entity_id")
            ->where('0 < redirect_type')
            ->where("'$this->oldUrlPath' = target_path");
        $bind = [
            'entity_type' => self::ENTITY_TYPE,
            'store_id' => $this->storeId,
            'entity_id' => $this->categoryId,
            'redirect_type' => '3%',
            'target_path' => $this->oldUrlPath,
        ];
        $urlRedirectIds = $this->connection->fetchCol($select, $bind);
        if ($urlRedirectIds) {
            $bind = ['target_path' => $this->preparedUrlPath];
            $where = ['url_rewrite_id IN(?)' => $urlRedirectIds];
            $this->connection->update($this->table, $bind, $where);

            return true;
        }

        return false;
    }

    /**
     * Returns table name
     *
     * @param string $name
     * @return string
     */
    public function getTable($name)
    {
        return $this->resource->getTableName($name);
    }

}
