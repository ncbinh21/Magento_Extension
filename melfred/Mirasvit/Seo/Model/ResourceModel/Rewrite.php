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



namespace Mirasvit\Seo\Model\ResourceModel;

class Rewrite extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $resourcePrefix;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string                                            $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mst_seo_rewrite', 'rewrite_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function loadStore(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_seo_rewrite_store'))
            ->where('rewrite_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('store_id', $array);
        }

        return $object;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassDelete()) {
            $object = $this->loadStore($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Call-back function.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $url = $object->getUrl();
        $url = trim($url);
        $object->setUrl($url);

        return parent::_beforeSave($object);
    }

    /**
     * Call-back function.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassStatus()) {
            $this->_saveToStoreTable($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve select object for load object data.
     *
     * @param string                                 $field
     * @param int|string|array                       $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->limit(1);

        return $select;
    }

    /**
     * @param string $object
     * @return bool
     */
    protected function _saveToStoreTable($object)
    {
        if (!$object->getData('stores')) {
            $condition = $this->getConnection()->quoteInto('rewrite_id = ?', $object->getId());
            $this->getConnection()->delete($this->getTable('mst_seo_rewrite_store'), $condition);

            $storeArray = [
                'rewrite_id' => $object->getId(),
                'store_id' => '0',
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_seo_rewrite_store'),
                $storeArray
            );

            return true;
        }

        $condition = $this->getConnection()->quoteInto('rewrite_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_seo_rewrite_store'), $condition);
        foreach ((array) $object->getData('stores') as $store) {
            $storeArray = [
                'rewrite_id' => $object->getId(),
                'store_id' => $store,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_seo_rewrite_store'),
                $storeArray
            );
        }
    }
}
