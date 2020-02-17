<?php

namespace Forix\CustomerOrder\Controller\Orders;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Manage
 * @package Forix\CustomerOrder\Controller\Orders
 */
class Manage extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Forix\CustomerOrder\Helper\Data
     */
    protected $dataHelper;

    /**
     * Manage constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Forix\CustomerOrder\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Forix\CustomerOrder\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->dataHelper->isDistributorManager()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Customer Orders'));
            return $resultPage;
        }
        return $this->resultFactory->create('redirect')->setUrl('/');
    }

}
