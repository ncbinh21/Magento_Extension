<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Controller\Adminhtml\Banner;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * ExportCsv action.
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class ExportCsv extends \Forix\Bannerslider\Controller\Adminhtml\Banner
{
    public function execute()
    {
        $fileName = 'images.csv';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Forix\Bannerslider\Block\Adminhtml\Banner\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
