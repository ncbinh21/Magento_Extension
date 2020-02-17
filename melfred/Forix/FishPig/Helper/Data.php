<?php

namespace Forix\FishPig\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Block\Product\Context as productContext;
use FishPig\WordPress\Model\ImageFactory;

class Data extends AbstractHelper
{

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	protected $_filesystem ;

	protected $_imageFactory;

	protected $_fishbigImageFactory;


	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		productContext $productContext,
		ImageFactory $fishbigImageFactory
	)
	{
		parent::__construct($context);
		$this->_filesystem          = $filesystem;
		$this->_imageFactory        = $imageFactory;
		$this->_storeManager        = $productContext->getStoreManager();
		$this->_fishbigImageFactory = $fishbigImageFactory;
	}

	/**
	 * Get configuration value
	 * @param $value
	 * @return mixed
	 */
	public function getConfigValue($value)
	{
		return $this->scopeConfig->getValue($value);
	}

	public function resize($image, $width = null, $height = null)
	{
		if ($image!="") {

			$pathResize = substr($image,strrpos($image,"uploads/")+8);
			$imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('mfblog/resized/'.$width.'/').$pathResize;
			if (!is_file($imageResized)) {
				$absolutePath = $image;
				$imageResize = $this->_imageFactory->create();
				$imageResize->open($absolutePath);
				$imageResize->constrainOnly(TRUE);
				$imageResize->keepTransparency(TRUE);
				$imageResize->keepFrame(FALSE);
				$imageResize->keepAspectRatio(TRUE);
				$imageResize->resize($width, $height);
				//destination folder
				$destination = $imageResized ;
				//save image
				$imageResize->save($destination);
			}
			$resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'mfblog/resized/'.$width.'/'.$pathResize;
			return $resizedURL;

		}

	}

	public function getBaseUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl();
	}

	public function getMediaUrl() {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}



}