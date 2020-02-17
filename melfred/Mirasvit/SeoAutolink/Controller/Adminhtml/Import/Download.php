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


namespace Mirasvit\SeoAutolink\Controller\Adminhtml\Import;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Download sample file controller
 */
class Download extends \Mirasvit\SeoAutolink\Controller\Adminhtml\Import
{
    const SAMPLE_FILES_MODULE = 'Mirasvit_SeoAutolink';

    /**
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($resource, $filesystem, $fileUploaderFactory, $context, $storeManager);

        $this->fileFactory        = $fileFactory;
        $this->resultRawFactory   = $resultRawFactory;
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file') . '.csv';
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, self::SAMPLE_FILES_MODULE);
        $fileAbsolutePath = realpath($moduleDir . '/../../pub/media/seo/') . '/' . $fileName;

        if (!file_exists($fileAbsolutePath)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addErrorMessage(__('There is no sample file for this entity.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        }

        $fileSize = filesize($fileAbsolutePath);

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents(file_get_contents($fileAbsolutePath));

        return $resultRaw;
    }
}
