<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Controller\Adminhtml\CoreSlider;

/**
 * Core slider Grid action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Grid extends \Forix\Bannerslider\Controller\Adminhtml\CoreSlider
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();

        return $resultLayout;
    }
}
