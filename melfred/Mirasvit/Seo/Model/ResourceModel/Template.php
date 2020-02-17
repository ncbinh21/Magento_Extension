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

class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     *
     */
    protected function _construct()
    {
        $this->_init('mst_seo_template', 'template_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function loadStore(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_seo_template_store'))
            ->where('template_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    /**
     * @param string $object
     *
     * @return void
     */
    protected function saveStore($object)
    {
        $condition = $this->getConnection()->quoteInto('template_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_seo_template_store'), $condition);
        foreach ((array) $object->getData('store_ids') as $store) {
            $storeArray = [
                'template_id' => $object->getId(),
                'store_id' => $store,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_seo_template_store'),
                $storeArray
            );
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassDelete()) {
            $this->loadStore($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassStatus()) {
            $this->_saveRule($object);
            $this->saveStore($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return void
     */
    protected function _saveRule(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('rule') && is_array($object->getData('rule'))) {
            $ruleData = $object->getData('rule');
            $model = $object->getRule();
            $model->setTemplateId($object->getId())
                ->loadPost($ruleData)
                ->save();
        }
    }
}
