<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Controller\Adminhtml;

/**
 * Report Abstract Action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
abstract class Report extends \Forix\Bannerslider\Controller\Adminhtml\AbstractAction
{
    /**
     * Check if admin has permissions to visit related pages.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Forix_Bannerslider::bannerslider_reports');
    }
}
