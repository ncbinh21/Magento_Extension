<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Makarovsoft\Purchasehistory\Controller\Adminhtml\Export;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Products extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Export invoiced report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = __('purchased_products_by_%1.csv', $this->getRequest()->getParam('id'));
        $grid = $this->_view->getLayout()->createBlock('Makarovsoft\Purchasehistory\Block\Adminhtml\Products');
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
