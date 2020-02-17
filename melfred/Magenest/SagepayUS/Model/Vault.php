<?php

namespace Magenest\SagepayUS\Model;

use Magenest\SagepayUS\Model\ResourceModel\Vault as Resource;
use Magenest\SagepayUS\Model\ResourceModel\Vault\Collection as Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Vault extends AbstractModel
{
    public function __construct(
        Context $context,
        Registry $registry,
        Resource $resource,
        Collection $resourceCollection,
        $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getCardType(){
        if($this->getData('card_type') == '3'){
            return "American Express";
        }
        if($this->getData('card_type') == '4'){
            return "Visa";
        }
        if($this->getData('card_type') == '5'){
            return "MasterCard";
        }
        if($this->getData('card_type') == '6'){
            return "Discover";
        }
        return $this->getData('card_type');
    }

    public function isOwnCard($customerId){
        return $this->getCustomerId()==$customerId;
    }
}
