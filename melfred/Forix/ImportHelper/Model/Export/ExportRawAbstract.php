<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/10/17
 * Time: 3:30 PM
 */

namespace Forix\ImportHelper\Model\Export;


abstract class ExportRawAbstract implements ExportRawInterface
{
    protected $_collectionFactory;

    /**
     * @var \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    protected $_collection;

    public function __construct(
        \Forix\ImportHelper\Model\ResourceModel\RawData\CollectionFactory $collectionFactory
    )
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param $collection \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    protected function addFieldToFilter($collection)
    {
        return $collection;
    }


    public function getCollection($resetCollection = false)
    {
        if (!($this->_collection) || $resetCollection) {
            $this->_collection = $this->_collectionFactory->create();
            $this->_collection->addFieldToSelect('*');
            $this->_collection = $this->addFieldToFilter($this->_collection);
        }
        return $this->_collection;
    }
}