<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: Soft Star Shoes
 * Date: 09 Feb 2018
 * Time: 4:14 PM
 */

namespace Forix\ProductListTemplate\Helper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreFactory;
use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_registry;
    protected $storeFactory;
    public function __construct(
        Context $context,
        StoreFactory $storeFactory = null,
        Registry $registry)
    {
        $this->_registry = $registry;
        parent::__construct($context);
        $this->storeFactory = $storeFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreFactory::class);
    }

    public function initStore(){
        if(!$this->_registry->registry('current_store')) {
            $store = $this->storeFactory->create();
            $store->load($this->_getRequest()->getParam('store', 0));
            $this->_registry->register('current_store', $store);
            return $store;
        }
        return $this->_registry->registry('current_store');
    }
}