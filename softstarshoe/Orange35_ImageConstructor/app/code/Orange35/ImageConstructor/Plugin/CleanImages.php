<?php

namespace Orange35\ImageConstructor\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;

class CleanImages
{
    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDatabase;

    /**
     * @var /Orange35\ImageConstructor\Helper\Image
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_mediaDirectory;

    /**
     * CleanImages constructor.
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Orange35\ImageConstructor\Helper\Image $helper
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Orange35\ImageConstructor\Helper\Image $helper
    )
    {
        $this->_coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_helper = $helper;
    }


    public function aroundClearCache(\Magento\Catalog\Model\Product\Image $subject, callable $proceed)
    {
        $dirsToClear = [
            $this->_helper->getBaseCachePath(),
            $this->_helper->getBaseTmpPath()
        ];

        foreach ($dirsToClear as $dir) {
            $this->_mediaDirectory->delete($dir);
            $this->_coreFileStorageDatabase->deleteFolder($this->_mediaDirectory->getAbsolutePath($dir));

        }
        $proceed();
    }

}