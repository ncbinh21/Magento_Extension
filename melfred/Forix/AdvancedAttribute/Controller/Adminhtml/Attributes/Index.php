<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\AdvancedAttribute\Controller\Adminhtml\Attributes;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Tax\Api\TaxRuleRepositoryInterface $ruleService
     * @param \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $taxRuleDataObjectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry

    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Forix_AdvancedAttribute::manage_attribute_banner')
            ->addBreadcrumb(__('Attributes Banners'), __('Attributes Banners'))
            ->addBreadcrumb(__('Attributes'), __('Attributes'));

        $resultPage->getConfig()->getTitle()->prepend(__('Attributes List'));
        return $resultPage;
    }
}
