<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\ResourceModel;

use Magento\Framework\DataObject;
use Aheadworks\Helpdesk\Model\Serializer;

/**
 * Class Ticket
 * @package Aheadworks\Helpdesk\Model\ResourceModel
 */
class Ticket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const UID_FIELD = 'uid';

    /**
     * Filed list for serialization
     *
     * @var array
     */
    protected $_serializableFields = ['cc_recipients' => [[],[]]];

    /**
     * Store repository
     *
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var Serializer
     */
    private $serializerModel;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param Serializer $serializerModel
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        Serializer $serializerModel,
        $connectionName = null
    ) {
        $this->storeRepository = $storeRepository;
        $this->serializerModel = $serializerModel;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_helpdesk_ticket', 'id');
    }

    /**
     * Check exist uid
     * @param $key
     * @return bool
     */
    public function ifUidExist($key)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable(),
            ['cnt' => 'COUNT(*)']
        )->where('`' . self::UID_FIELD . '`' . '=?', $key);
        $lookup = $connection->fetchRow($select);
        if (empty($lookup)) {
            return false;
        }
        return $lookup['cnt'] > 1;
    }

    /**
     * Before save method
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $now = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time());
        if (!$object->getId()) {
            $object->setCreatedAt($now);
        }
        return parent::_beforeSave($object);
    }

    /**
     * After load method
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        try {
            $storeData = $this->storeRepository->getById($object->getStoreId());
            $websiteId = $storeData->getWebsiteId();
        } catch (\Exception $e) {
            $websiteId = null;
        }

        $object->setWebsiteId($websiteId);
        return parent::_afterLoad($object);
    }

    /**
     * @inheritdoc
     */
    protected function _serializeField(DataObject $object, $field, $defaultValue = null, $unsetEmpty = false)
    {
        $value = $object->getData($field);
        if (empty($value) && $unsetEmpty) {
            $object->unsetData($field);
        } else {
            $object->setData($field, $this->serializerModel->serialize($value ?: $defaultValue));
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _unserializeField(DataObject $object, $field, $defaultValue = null)
    {
        $value = $object->getData($field);
        if ($value) {
            $value = $this->serializerModel->unserialize($object->getData($field));
            if (empty($value)) {
                $object->setData($field, $defaultValue);
            } else {
                $object->setData($field, $value);
            }
        } else {
            $object->setData($field, $defaultValue);
        }
    }
}
