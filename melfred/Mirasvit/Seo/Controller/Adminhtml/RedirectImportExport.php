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



namespace Mirasvit\Seo\Controller\Adminhtml;

abstract class RedirectImportExport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Mirasvit\Seo\Model\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @param \Magento\Framework\App\ResourceConnection        $resource
     * @param \Magento\Framework\Filesystem                    $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Mirasvit\Seo\Model\RedirectFactory              $redirectFactory
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Mirasvit\Seo\Model\RedirectFactory $redirectFactory
    ) {
        $this->resource = $resource;
        $this->filesystem = $filesystem;
        $this->context = $context;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->resultFactory = $context->getResultFactory();
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
        $this->redirectFactory = $redirectFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Seo::seo_redirect_import_export');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('Mirasvit_Seo::seo');

        return $this;
    }
}
