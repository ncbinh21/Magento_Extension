<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Controller\Adminhtml\Slider;

/**
 * Slider Index action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Index extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');

            return $resultForward;
        }

        $resultPage = $this->_resultPageFactory->create();

        /*
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(__('Sliders'), __('Sliders'));
        $this->_addBreadcrumb(__('Manage Sliders'), __('Manage Sliders'));

        return $resultPage;
    }
}
