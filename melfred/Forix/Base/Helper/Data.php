<?php

namespace Forix\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;


class Data extends AbstractHelper
{
    /**
     * @var Monolog|\Magento\Framework\Logger\Monolog
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlinterface;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    protected $_jsonDecode;

    protected $_customerSession;

	protected $_productRepositoryFactory;

	protected $_filesystem ;

	protected $_imageFactory;

	protected $_filterProvider;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\Json\DecoderInterface $jsonDecoder,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider
    )
    {
        $this->_logger = $logger;
        $this->moduleManager = $moduleManager;
        $this->_storeManager = $storeManager;
        $this->_httpContext = $httpContext;
        $this->_urlinterface = $context->getUrlBuilder();
		$this->_jsonDecode   = $jsonDecoder;
		$this->_customerSession = $customerSession;
		$this->_productRepositoryFactory = $productRepositoryFactory;
		$this->_filesystem          = $filesystem;
		$this->_imageFactory        = $imageFactory;
		$this->_filterProvider      = $filterProvider;
        parent::__construct($context);
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

    /**
     * Check if module enabled
     * @param $module
     * @return bool
     */
    public function isModuleEnabled($module)
    {
        return $this->_moduleManager->isEnabled($module);
    }

    /**
     * Get base url
     * Possible types: link, direct_link, web, media, static, js
     *
     * Example: ['_type' => 'media']
     * @param array $params
     */
    public function getBaseUrl($params = [])
    {

        return $this->_urlBuilder->getBaseUrl($params);
    }

    /**
     * Get current store id
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get website id
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Get Store code
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * Get Store name
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get the current url of recently viewed page
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlinterface->getCurrentUrl();
    }

    /**
     * Check if store is active
     *
     * @return boolean
     */
    public function isStoreActive()
    {
        return $this->_storeManager->getStore()->isActive();
    }

    /**
     * Get param
     * @param $param
     * @return mixed
     */
    public function getParam($param)
    {
        return $this->_request->getParam($param);
    }

    /**
     * Get all params
     * @return array
     */
    public function getParams()
    {
        return $this->_request->getParams();
    }

    /**
     * Check if frontend URL is secure
     * @return boolean
     */
    public function isFrontUrlSecure()
    {
        return $this->_storeManager->getStore()->isFrontUrlSecure();
    }

    /**
     * Check if current requested URL is secure
     * @return boolean
     */
    public function isCurrentlySecure()
    {
        return $this->_storeManager->getStore()->isCurrentlySecure();
    }

    //Get Controller, Module, Action & Route Name
    public function getControllerModule()
    {
        return $this->_request->getControllerModule();
    }

    public function getFullActionName()
    {
        return $this->_request->getFullActionName();
    }

    public function getRouteName()
    {
        return $this->_request->getRouteName();
    }

    public function getActionName()
    {
        return $this->_request->getActionName();
    }

    public function getControllerName()
    {
        return $this->_request->getControllerName();
    }

    public function getModuleName()
    {
        return $this->_request->getModuleName();
    }

    /**
     * log
     * @param $message
     */
    public function log($message)
    {
        $this->_logger->log(Monolog::INFO, $message);
    }

    public function debug($message)
    {
        $this->_logger->debug(Monolog::DEBUG, $message);
    }

    public function critical($message)
    {
        $this->_logger->critical(Monolog::CRITICAL, $message);
    }

    protected function isRequisitionDisplay()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getRequisitionClass()
    {
        return $this->isRequisitionDisplay() ? 'has-requisition' : '';
    }

    /**
     * Whether the B2B feature is enabled or not
     *
     * @return bool
     */
    public function isB2BEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_Company');
    }

	public function getMediaUrl()
	{
		$currentStore = $this->_storeManager->getStore();
		return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
	}

	public function JsonDecode($data)
	{
		return $this->_jsonDecode->decode($data);
	}

	public function setParams($params)
	{
		if (!empty($params)) {
			$this->_request->setParams($params);
		}
	}

	public function isUserLogin()
	{
		$status = $this->_customerSession->isLoggedIn();
		if ($status) {
			return true;
		}
		return false;
	}

	public function getUrlBase()
	{
		return $this->_storeManager->getStore()->getBaseUrl();
	}

	public function getImageProduct($id,$type="thumbnail") {
    	// $type: image, thumbnail, small_image
		$product = $this->_productRepositoryFactory->create()->getById($id);
		return $this->getMediaUrl().'catalog/product'.$product->getData($type);
	}

	public function resize($image, $width = null, $height = null)
	{

		if ($image!="") {
			$pathResize = substr($image,strrpos($image,"catalog/product/")+16);
			$imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('catalog/product/'.$width.'/').$pathResize;
			$source = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('catalog/product/'.$pathResize);
			if (is_file($source)) {
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
				$resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product/'.$width.'/'.$pathResize;
				return $resizedURL;
			}
		}
	}

	public function convertImageEditor($content) {
		return $this->_filterProvider->getPageFilter()->filter($content);
	}

}