<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Seo\Controller\Adminhtml\RedirectImportExport;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Export extends \Mirasvit\Seo\Controller\Adminhtml\RedirectImportExport
{
    /**
     * Export redirect action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $headers = new \Magento\Framework\DataObject(
            [
                'url_from' => 'url_from',
                'url_to' => 'url_to',
                'redirect_type' => 'redirect_type',
                'is_redirect_only_error_page' => 'is_redirect_only_error_page',
                'comments' => 'comments',
                'is_active' => 'is_active',
                'store_id' => 'store_id',
            ]
        );

        $template = '"{{url_from}}","{{url_to}}","{{redirect_type}}","{{is_redirect_only_error_page}}","{{comments}}"' .
            ',"{{is_active}}","{{store_id}}"';
        $content = $headers->toString($template);

        $content .= "\n";

        $collection = $this->redirectFactory->create()->getCollection();

        $readConnection = $this->resource->getConnection('core_read');
        $storeTable = $this->resource->getTableName('mst_seo_redirect_store');
        $collection->getSelect()
            ->join(
                ['store_table' => $storeTable ],
                'main_table.redirect_id = store_table.redirect_id'
            )
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['main_table.*', 'GROUP_CONCAT(store_table.store_id SEPARATOR "/") as store_id'])
            ->group('main_table.redirect_id');

        while ($item = $collection->fetchItem()) {
            $content .= $item->toString($template) . "\n";
        }

        return $this->fileFactory->create('redirects.csv', $content, DirectoryList::VAR_DIR);
    }
}