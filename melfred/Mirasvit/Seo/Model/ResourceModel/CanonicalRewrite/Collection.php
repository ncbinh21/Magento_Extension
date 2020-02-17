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



namespace Mirasvit\Seo\Model\ResourceModel\CanonicalRewrite;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\Seo\Model\CanonicalRewrite::class,
            \Mirasvit\Seo\Model\ResourceModel\CanonicalRewrite::class
        );

        $this->_idFieldName = CanonicalRewriteInterface::ID;
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter(CanonicalRewriteInterface::IS_ACTIVE, 1);

        return $this;
    }

    /**
     * @return $this
     */
    public function addSortOrder()
    {
        $this->getSelect()
            ->order(new \Zend_Db_Expr(CanonicalRewriteInterface::SORT_ORDER . ' asc, '
                . CanonicalRewriteInterface::ID . ' asc'));

        return $this;
    }

    /**
     * @param string $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        $this->getSelect()
            ->joinLeft(
                ['store_table' => $this->getTable(CanonicalRewriteStoreInterface::TABLE_NAME)],
                'main_table.' . CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID
                . ' = store_table.' . CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID,
                []
            )
            ->where('store_table.' . CanonicalRewriteStoreInterface::STORE_ID . ' in (?)', [0, $store]);

        return $this;
    }

    /**
     * @return $this
     */
    public function addStoreColumn()
    {
        $this->getSelect()
            ->columns(
                [CanonicalRewriteStoreInterface::STORE_ID => new \Zend_Db_Expr(
                    "(SELECT GROUP_CONCAT(" . CanonicalRewriteStoreInterface::STORE_ID
                    . ") FROM `{$this->getTable(CanonicalRewriteStoreInterface::TABLE_NAME)}`
                    AS `seo_canonical_rewrite_store_table`
                    WHERE main_table." . CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID
                    . " = seo_canonical_rewrite_store_table."
                    . CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID . ")")]
            );

        return $this;
    }
}
