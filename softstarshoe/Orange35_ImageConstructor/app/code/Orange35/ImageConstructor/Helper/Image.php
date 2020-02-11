<?php

namespace Orange35\ImageConstructor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Filesystem;
use \Magento\Framework\Image\AdapterFactory;


class Image extends AbstractHelper
{
    /**
     * @var string
     */
    const IMAGE_TMP_PATH = 'tmp/catalog/layer';
    /**
     * @var string
     */
    const IMAGE_CACHE_PATH = 'catalog/layer/cache';
    /**
     * @var string
     */
    const IMAGE_PATH = 'catalog/layer';

    /**
     * Base tmp path
     *
     * @var string
     */
    public $baseTmpPath;

    /**
     * Base cache path
     *
     * @var string
     */
    public $baseCachePath;

    /**
     * Base path
     *
     * @var string
     */
    public $basePath;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_directory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;
    
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        $baseTmpPath,
        $baseCachePath,
        $basePath

    )
    {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->baseTmpPath = $baseTmpPath;
        $this->baseCachePath = $baseCachePath;
        $this->basePath = $basePath;

    }

    /**
     * @param $layer
     * @param int|null $width
     * @param int|null $height
     * @param bool $keepFrame
     *
     * @return string
     */
    public function getImageUrl(
        $layer,
        $width = null,
        $height = null,
        $keepFrame = null
    )
    {
        if (is_null($width) && is_null($height)) {
            return $this->getBaseUrl() . $this->getBasePath() . $layer;
        }
        $resizedImagePath = $this->_filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($this->getCachePath($width, $height)) . $layer;
        if (file_exists($resizedImagePath)) {
            return $this->getBaseUrl() . $this->getCachePath($width, $height) . $layer;
        }
        return $this->resizeImage($layer, $resizedImagePath, $width, $height, (bool)$keepFrame);
    }

    /**
     * @param $layer string layer value
     * @param $resizedImagePath string
     * @param int $width
     * @param int $height
     * @param bool $keepFrame
     * @return string
     */
    protected function resizeImage(
        $layer,
        $resizedImagePath,
        $width = 200,
        $height = null,
        $keepFrame = false
    )
    {
        $imagePath = $this->_filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . $this->getBasePath() . $layer;

        $imageResize = $this->_imageFactory->create();
        $imageResize->open($imagePath);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame($keepFrame);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        $imageResize->save($resizedImagePath);

        $resizedURL = $this->getBaseUrl() . $this->getCachePath($width, $height) . $layer;
        return $resizedURL;

    }

    /**
     * Set base tmp path
     *
     * @param string $baseTmpPath
     *
     * @return void
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }

    /**
     * Set base path
     *
     * @param string $basePath
     *
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->_storeManager
            ->getStore()
            ->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );
    }

    /**
     * Retrieve base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Set base cache path
     *
     * @param string $baseCachePath
     *
     * @return void
     */
    public function setBaseCachePath($baseCachePath)
    {
        $this->baseCachePath = $baseCachePath;
    }

    /**
     * Retrieve base cache path
     *
     * @return string
     */
    public function getBaseCachePath()
    {
        return $this->baseCachePath;
    }

    /**
     * @param $width integer
     * @param $height integer
     * @return string
     */
    public function getCachePath($width, $height)
    {
        return $this->baseCachePath . '/' . $width . 'x' . $height;
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    public function getFilePath($path, $name)
    {
        return rtrim($path, '/') . '/' . ltrim($name, '/');
    }


}
