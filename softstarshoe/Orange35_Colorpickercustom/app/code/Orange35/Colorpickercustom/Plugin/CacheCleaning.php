<?php

namespace Orange35\Colorpickercustom\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Catalog\Model\Product\Image;
use Magento\Framework\Filesystem;
use Orange35\Colorpickercustom\Helper\Image as ImageHelper;

class CacheCleaning
{
    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDatabase;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_mediaDirectory;

    /**
     * @var \Orange35\Colorpickercustom\Helper\Image
     */
    protected $_helper;

    /**
     * CacheCleaning constructor.
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param ImageHelper $helper
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        ImageHelper $helper
    )
    {
        $this->_coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_helper = $helper;
    }

    public function aroundClearCache(Image $subject, callable $proceed)
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