<?php

namespace Magenest\SagepayUS\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Helper\Session\CurrentCustomer;

class Card extends \Magento\Framework\View\Element\Template
{
    protected $vaultFactory;

    protected $_currentCustomer;

    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        \Magenest\SagepayUS\Model\VaultFactory $vaultFactory,
        array $data
    ) {
       $this->_currentCustomer = $currentCustomer;
       $this->vaultFactory = $vaultFactory;
        parent::__construct($context, $data);
    }

    public function getDataCard()
    {
        $customerId = $this->_currentCustomer->getCustomerId();
        $vaultCollection = $this->vaultFactory->create()->getCollection()->addFieldToFilter("customer_id", $customerId);
        return $vaultCollection;
    }

    public function getDeleteUrl($id)
    {
        return $this->getUrl('sagepayus/card/delete', ['id' => $id]);
    }
}
