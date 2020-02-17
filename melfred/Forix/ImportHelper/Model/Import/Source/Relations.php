<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 1:36 PM
 */

namespace Forix\ImportHelper\Model\Import\Source;


class Relations
{

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $_relationCollection;
    protected $_relationCollectionFactory;
    protected $_abc;
    public function __construct(
        \Magento\Framework\Data\CollectionFactory $relationCollectionFactory
    )
    {
        $this->_relationCollectionFactory = $relationCollectionFactory;
        $this->_abc = uniqid();
    }

    public function addItem(array $data){
        $this->getRelationCollection()->addItem(new \Magento\Framework\DataObject($data));
    }

    /**
     * @return \Magento\Framework\Data\Collection
     */
    public function getRelationCollection(){
        if(!$this->_relationCollection){
            $this->_relationCollection = $this->_relationCollectionFactory->create();
        }
        return $this->_relationCollection;
    }
}