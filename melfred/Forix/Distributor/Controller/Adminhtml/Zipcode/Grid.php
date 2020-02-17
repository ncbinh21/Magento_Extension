<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Distributor\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action;

class Grid extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Forix_Distributor::zipcode';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_locationFactory;
    protected $_coreRegistry;

    public function __construct(Action\Context $context,
                                \Magento\Framework\Registry $coreRegistry,
                                \Amasty\Storelocator\Model\LocationFactory $locationFactory,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_locationFactory = $locationFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    protected function initLocation()
    {
        if ($this->getRequest()->getParam('id')) {
            $model = $this->_locationFactory->create()->load($this->getRequest()->getParam('id'));
            if ($model->getId()) {
                $this->_coreRegistry->register('current_amasty_storelocator_location', $model);
            }
        }
    }


    /**
     * Product grid for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->initLocation();
        return $this->resultPageFactory->create();
    }
}
