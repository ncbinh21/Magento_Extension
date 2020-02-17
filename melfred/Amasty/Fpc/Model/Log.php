<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Log
 *
 * @package Amasty\Fpc\Model
 *
 * @method ResourceModel\Log getResource()
 */
class Log extends AbstractModel
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var ResourceModel\Log\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var LogFactory
     */
    private $logFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Config $config,
        \Amasty\Fpc\Model\ResourceModel\Log\CollectionFactory $collectionFactory,
        LogFactory $logFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;
        $this->logFactory = $logFactory;
    }

    protected function _construct()
    {
        $this->_init('Amasty\Fpc\Model\ResourceModel\Log');
    }

    /**
     * Delete all records that exceeds "Log Size" limit
     *
     * @return $this
     */
    public function trim()
    {
        $maxSize = $this->config->getValue('crawler/log_size');

        /** @var ResourceModel\Log\Collection $collection */
        $collection = $this->collectionFactory->create();

        $limit = $collection->getSize() - $maxSize;

        $this->getResource()->deleteWithLimit($limit);

        return $this;
    }

    public function add($data)
    {
        /** @var Log $record */
        $record = $this->logFactory->create();

        $record->setData($data);

        $this->getResource()->save($record);

        return $this;
    }
}
