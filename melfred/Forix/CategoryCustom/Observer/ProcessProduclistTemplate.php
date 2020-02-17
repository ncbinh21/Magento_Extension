<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\CategoryCustom\Observer;
use Magento\Framework\Event\ObserverInterface;
class ProcessProduclistTemplate implements ObserverInterface{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * Constructor
     *
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($observer->getFullActionName() == 'catalog_category_view'){
            $currentCategory = $this->_coreRegistry->registry('current_category');
            if(is_object($currentCategory) && $currentCategory->getId()){
                $handle = $currentCategory->getForixProductListTemplate() ? $currentCategory->getForixProductListTemplate() : '';
                if($handle){
                    $observer->getLayout()->getUpdate()->addHandle($handle);
                }
            }
        }
        return $this;
    }
}