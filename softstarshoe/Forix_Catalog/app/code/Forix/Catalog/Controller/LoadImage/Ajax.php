<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Date: 20/03/2018
 */
namespace Forix\Catalog\Controller\LoadImage;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class Ajax extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_SECTION = 'image_constructor_section/';
    const MINI_CART = 'mini_cart/';
    const CART = 'cart/';
    const CHECKOUT = 'checkout/';
    const WISHLIST = 'wishlist/';
    const MINI_WISHLIST = 'mini_wishlist/';

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var
     */
    protected $helperImage;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * Ajax constructor.
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CheckoutSession $checkoutSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->imageFactory = $imageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    protected function getGroup(){
        return $this->group;
    }
    protected function setGroup($group){
        $this->group = $group;
    }
    /**
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Exception('Wrong request.');
        }
        $isCart = $this->getRequest()->getParam('cardId');
        $group = $this->getRequest()->getParam('group');
        $this->setGroup($group);
        if ($isCart){
            $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        }
        $imageHeight = $this->getFromSystem('image_width');
        $imageWidth = $this->getFromSystem('image_height');
        $keepFrame = (bool)$this->getFromSystem('keep_frame');
        $optionArr = [];
        foreach ($items as $item) {
            $product = $item->getProduct();
            $buyRequest = $item->getBuyRequest();
            if ($buyRequest->hasOptions()) {
                $chosenValues = $this->createValues($buyRequest->getOptions());
                $options = [];
                foreach ($product->getOptions() as $option) {
                    if (is_array($option->getValues())) {
                        foreach ($option->getValues() as $value) {
                            if (!is_null($value->getLayer()) && in_array($value->getOptionTypeId(), $chosenValues)) {
                                $options['layers'][] = [
                                    'optionId' => $value->getOptionTypeId(),
                                    'url' => $this->getImageUrl($value->getLayer(), $imageWidth, $imageHeight, $keepFrame),
                                    'sortOrder' => $value->getSortOrder(),
                                    'sortOrderOption' => $option->getSortOrder(),
                                ];
                            }
                        }
                    }
                }
                $optionArr['items'][$item->getId()] = $options;
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($optionArr)
        );
    }

    /**
     * @param $option
     * @return mixed
     */
    protected function getFromSystem($option){
        return $this->scopeConfig->getValue(self::XML_PATH_SECTION. $this->getGroup() . $option, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $options
     * @return array
     */
    public function createValues($options)
    {
        $values = [];
        foreach ($options as $option) {
            if (is_array($option)) {
                foreach ($option as $value) {
                    $values[] = $value;
                }
            } else {
                $values[] = $option;
            }
        }
        return $values;
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
            return $this->getBaseUrl() . self::IMAGE_PATH . $layer;
        }
        $resizedImagePath = $this->filesystem
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
        $imagePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . self::IMAGE_PATH . $layer;

        $imageResize = $this->imageFactory->create();
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
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );
    }
    /**
     * @param $width integer
     * @param $height integer
     * @return string
     */
    public function getCachePath($width, $height)
    {
        return self::IMAGE_CACHE_PATH . '/' . $width . 'x' . $height;
    }
}