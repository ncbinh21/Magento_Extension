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



namespace Mirasvit\Seo\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Magento\MediaStorage\Helper\File\Storage\Database as StorageDatabase;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;
use Mirasvit\Seo\Helper\Version;

/**
 * Flush cache if Image Friendly Urls enabled and push "Flush Catalog Images Cache" button
 */
class FlushImagesCacheObserver implements ObserverInterface
{
    /**
     * @param MediaConfig $catalogProductMediaConfig
     * @param ImageConfigServiceInterface $imageConfigService
     * @param StorageDatabase $coreFileStorageDatabase
     * @param Filesystem $filesystem
     */
    public function __construct(
        Version $version,
        ImageConfigServiceInterface $imageConfigService,
        StorageDatabase $coreFileStorageDatabase,
        Filesystem $filesystem
    ) {
        $this->version = $version;
        $this->imageConfigService = $imageConfigService;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->imageConfigService->isEnableImageFriendlyUrl()) {
            if ($this->version->getVersion() >= '2.2.0') {
                $directory = $this->mediaDirectory->getAbsolutePath(ImageConfigServiceInterface::MEDIA_PATH);
            } else {
                $directory = ImageConfigServiceInterface::MEDIA_PATH;
            }
            $this->mediaDirectory->delete($directory);
            $this->coreFileStorageDatabase->deleteFolder($this->mediaDirectory->getAbsolutePath($directory));
        }
    }

}
