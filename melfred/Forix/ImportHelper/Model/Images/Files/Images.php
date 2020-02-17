<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 1:57 PM
 */
namespace Forix\ImportHelper\Model\Images\Files;
use Forix\ImportHelper\Model\Images\AbstractImagesIO;
use Magento\Framework\App\Filesystem\DirectoryList;
class Images extends AbstractImagesIO
{
    protected $_mediaDirectory;
    protected $_folderPath = '';
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        $folderPath
    )
    {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_folderPath = $this->_mediaDirectory->getAbsolutePath($folderPath);
    }

    public function getImageRealPath($imageName)
    {
        // TODO: Implement getImageRealPath() method.
        return rtrim($this->_folderPath, "/") . "/" . ltrim(trim($imageName), "/");
    }
}