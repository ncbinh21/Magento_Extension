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

class Import extends \Mirasvit\Seo\Controller\Adminhtml\RedirectImportExport
{
    /**
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $existingStoreIds = [0];
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $existingStoreIds[] = $store->getId();
        }

        /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
        $uploader = $this->fileUploaderFactory->create(['fileId' => 'import_redirect_file']);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(true);
        $path = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
                ->getAbsolutePath().'/import';
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }

        try {
            $result = $uploader->save($path);
            $fullPath = $result['path'].'/'.$result['file'];

            $file = new \Magento\Framework\Filesystem\Driver\File;
            $file->isFile($fullPath);
            $csv = new \Magento\Framework\File\Csv($file);
            $data = $csv->getData($fullPath);

            $items = [];
            if (count($data) > 1) {
                for ($i = 1; $i < count($data); ++$i) {
                    $item = [];
                    for ($j = 0; $j < count($data[0]); ++$j) {
                        if (isset($data[$i][$j]) && trim($data[$i][$j]) != '') {
                            $preparedKey = preg_replace('/[[:^print:]]/', "", $data[0][$j]); //delete invisible symbols
                            $item[strtolower($preparedKey)] = $data[$i][$j];
                        }
                    }
                    $items[] = $item;
                }
            }

            $resource = $this->resource;
            $writeConnection = $resource->getConnection('core_write');
            $table = $resource->getTableName('mst_seo_redirect');
            $tableB = $resource->getTableName('mst_seo_redirect_store');
            $i = 0;
            foreach ($items as $item) {
                if (!isset($item['url_from']) || !isset($item['url_to'])) {
                    continue;
                }
                $item = new \Magento\Framework\DataObject($item);
                $query = "REPLACE {$table} SET
                    url_from = '".addslashes($item->getUrlFrom())."',
                    url_to = '".addslashes($item->getUrlTo())."',
                    redirect_type = '".addslashes($item->getRedirectType())."',
                    is_redirect_only_error_page = '".addslashes($item->getIsRedirectOnlyErrorPage())."',
                    comments = '".addslashes($item->getComments())."',
                    is_active = '".addslashes($item->getIsActive())."';
                     ";

                $writeConnection->query($query);
                $lastInsertId = $writeConnection->lastInsertId();
                $storeId = ($item->getStoreId()) ? $item->getStoreId() : 0;
                if (strpos($storeId, '/') !== false) { //we can use more than one store 1/2/3 etc.
                    $storeIds = [];
                    $storeIds = explode('/', $storeId);
                    $storeIds = array_intersect($storeIds, $existingStoreIds);
                }
                if ((!isset($storeIds) && !in_array($storeId, $existingStoreIds))
                    || (isset($storeIds) && !$storeIds)
                    || (isset($storeIds) && in_array(0, $storeIds)) ) {
                        $storeId = 0;
                        $storeIds = false;
                }
                if (isset($storeIds) && $storeIds) {
                    foreach ($storeIds as $storeId) {
                        $query = "REPLACE {$tableB} SET
                        store_id = " . $storeId . ",
                        redirect_id = " . $lastInsertId . ";";
                        $writeConnection->query($query);
                    }
                } else {
                    $query = "REPLACE {$tableB} SET
                        store_id = " . $storeId . ",
                        redirect_id = LAST_INSERT_ID();";
                    $writeConnection->query($query);
                }
                ++$i;
            }

            $this->messageManager->addSuccess(''.$i.' records were inserted or updated');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
