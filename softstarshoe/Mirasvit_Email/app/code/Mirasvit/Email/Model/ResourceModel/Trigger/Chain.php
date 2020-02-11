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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Model\ResourceModel\Trigger;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\Email\Api\Data\TriggerChainInterface;
use Mirasvit\Email\Model\Queue;
use Mirasvit\Email\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;

class Chain extends AbstractDb
{
    /**
     * @var QueueCollectionFactory
     */
    private $queueCollectionFactory;

    /**
     * Chain constructor.
     *
     * @param QueueCollectionFactory                            $queueCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null|string                                       $connectionName
     */
    public function __construct(
        QueueCollectionFactory $queueCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->queueCollectionFactory = $queueCollectionFactory;

        parent::__construct($context, $connectionName);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(TriggerChainInterface::TABLE_NAME, TriggerChainInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));

        if (is_array($object->getExcludeDays())) {
            $object->setExcludeDays(implode(',', $object->getExcludeDays()));
        }

        return parent::_beforeSave($object);
    }

    /**
     * {@inheritDoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var TriggerChainInterface|AbstractModel $object */
        $object->setExcludeDays($object->getExcludeDays());

        return parent::_afterLoad($object);
    }


    /**
     * Cancel queued emails that use this email chain.
     *
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $queueToDelete = $this->queueCollectionFactory->create()
            ->addFieldToFilter('status', QueueInterface::STATUS_PENDING)
            ->addFieldToFilter('chain_id', $object->getId());

        /** @var Queue $queue */
        foreach ($queueToDelete as $queue) {
            $queue->cancel(__('Associated email chain was removed from a trigger.'));
        }

        return $this;
    }
}
