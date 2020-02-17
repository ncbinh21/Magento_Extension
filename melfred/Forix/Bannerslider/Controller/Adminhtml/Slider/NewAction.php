<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Controller\Adminhtml\Slider;

/**
 * New Slider Action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class NewAction extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
