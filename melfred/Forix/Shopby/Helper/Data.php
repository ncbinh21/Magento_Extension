<?php

namespace Forix\Shopby\Helper;

use Magento\Framework\App\Helper\Context;
use Forix\Shopby\Model\ResourceModel\ResourceHelperFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Block\Product\Context as productContext;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as AmastyBrandCollection;
use Magento\Framework\Registry;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{


	protected $_request;
	protected $_resourceHelperFactory;
	protected $_scopeConfig;
	protected $_storeManager;
	protected $_directoryList;
	protected $_optionSetting;
	protected $_filesystem ;
	protected $_imageFactory;
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_registry;

	public function __construct(
		Context $context,
		Http $request,
        ResourceHelperFactory $resourceHelperFactory,
		ScopeConfigInterface $scopeConfig,
		productContext $productContext,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
		AmastyBrandCollection $optionSetting,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		Registry $registry
	)
	{
		parent::__construct($context);
		$this->_request = $request;
		$this->_resourceHelperFactory   = $resourceHelperFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager  = $productContext->getStoreManager();
		$this->_directoryList = $directoryList;
		$this->_optionSetting = $optionSetting;
		$this->_filesystem = $filesystem;
		$this->_imageFactory = $imageFactory;
		$this->_registry = $registry;
	}

	public function QueryProductWithAttribute($cate, $options) {
		$option_id = isset($options["option_id"]) ? $options["option_id"] : '';
		$query = $cate->getProductCollection()->addAttributeToFilter('mb_rig_model',['finset' => $option_id] );
		return $query->count();
	}

	public function getOptions()
	{
		$params = $this->_request->getParams();
		$result = "";
		if (isset($params["vermeer"])) {
			$label  = str_replace(['-','%20'],' ',$params["vermeer"]);
			$result = $this->_resourceHelperFactory->create()->getOptionIdByLabel($label);
		}
		return $result;
	}

	public function getImageSize($imageUrl) {

		$checkedImageUrl = [];
		$isAllowUrlFopen = ini_get('allow_url_fopen');
		$rootFolder = $this->_directoryList->getRoot();
		$postion  = strpos($imageUrl, 'media');
		$imageUrl = substr($imageUrl, $postion);
		if (is_file($rootFolder .'/pub/'. $imageUrl)) {
			$imageUrlDirect = $rootFolder .'/pub/'. $imageUrl;
			$imageUrlPub = $rootFolder . '/pub/' . $imageUrl;
		} else {
			$imageUrlDirect = $rootFolder .'/'. $imageUrl;
			$imageUrlPub = $rootFolder . '/' . $imageUrl;
		}
		if ($isAllowUrlFopen && file_exists($imageUrlDirect) && is_file($imageUrlPub)) {
			$checkedImageUrl = getimagesize($imageUrlDirect);
		} elseif ($isAllowUrlFopen && file_exists($imageUrlPub) && is_file($imageUrlPub)) {
			$checkedImageUrl = getimagesize($imageUrlPub);
		}

		return $checkedImageUrl;
	}

	public function getConfigValue($value) {
		if ($value!="") {
			return $this->_scopeConfig->getValue(
				$value,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
		}
	}

	public function getMediaUrl() {
		$currentStore = $this->_storeManager->getStore();
		return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
	}

	public function getRequestParams() {
		return $this->_request->getParams();
	}

	public function getAmastyOptionSetting($value) {
		$collection = $this->_optionSetting->create();
		$res = $collection->addFieldToFilter('value', $value);
		return $res->getFirstItem();
	}

	public function resize($image, $width = null, $height = null)
	{
		$absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('catalog/category/').$image;
		$imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'/').$image;
		if (is_file($absolutePath)) {
			if (!is_file($imageResized)) {
				//create image factory...
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

				$resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'/'.$image;
				return $resizedURL;
			}
		}
	}

	public function ConvertParam($string)
	{
		return str_replace(["%20"," ","X","/"],["-","-","x","_"], strtolower($string));
	}

	public function RevertParam($string,$isUpper=true)
	{
		if (!$isUpper) {
			return str_replace(["%20","-","X","_"],[" "," ","x","/"], $string);
		}
		return str_replace(["%20","-","X","_"],[" "," ","x","/"], strtoupper($string));
	}

	public function setRigModelRegister($data) {
	    //Hidro Remove
		$this->_registry->register("option_rig", $data);
	}

	public function getRigModelRegister() {
	    //Hidro Remove
		return $this->_registry->registry("option_rig");
	}

}