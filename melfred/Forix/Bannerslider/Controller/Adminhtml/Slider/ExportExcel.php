<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Controller\Adminhtml\Slider;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export Excel action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class ExportExcel extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    public function execute()
    {
        $fileName = 'sliders.xls';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Forix\Bannerslider\Block\Adminhtml\Slider\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
