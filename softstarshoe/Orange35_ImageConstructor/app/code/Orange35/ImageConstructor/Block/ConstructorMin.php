<?php

namespace Orange35\ImageConstructor\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Framework\Stdlib\ArrayUtils;
use \Magento\Framework\Json\EncoderInterface;
use \Magento\Wishlist\Model\WishlistFactory;
use Orange35\ImageConstructor\Helper\Image as HelperImage;


class ConstructorMin extends Template
{
    const XML_PATH_SECTION = 'image_constructor_section/';
    const MINI_CART = 'mini_cart/';
    const CART = 'cart/';
    const CHECKOUT = 'checkout/';
    const WISHLIST = 'wishlist/';
    const MINI_WISHLIST = 'mini_wishlist/';
    /**
     * @var HelperImage
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    protected $_wishlistFactory;
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    protected $_scopeConfig;
    protected $_storeScope;
    /**
     * @var current system group
     */
    protected $_group;

    /**
     * ConstructorMin constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param WishlistFactory $wishlistFactory
     * @param HelperImage $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        WishlistFactory $wishlistFactory,
        HelperImage $helper,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $data
        );
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->jsonEncoder = $jsonEncoder;
        $this->helper = $helper;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    protected function _getGroup(){
        return $this->_group;
    }
    protected function _setGroup($group){
        $this->_group = $group;
    }

    protected function getFromSystem($option){
        return $this->_scopeConfig->getValue(self::XML_PATH_SECTION. $this->_getGroup() . $option, $this->_storeScope);
    }

    /**
     * @param $group
     * @param bool $isCart
     * @return string
     */
    public function getOptionsJson($group, $isCart = true)
    {
        $this->_setGroup($group);
        $optionArr = $this->getOptions($isCart);
        $optionArr['cartSelector'] = $this->getFromSystem('items_block_selector'); //"[data-block='minicart']"
        $optionArr['cartItemSelector'] = $this->getFromSystem('item_block_selector'); //".minicart-items .product-item";
        $optionArr['productImageWrapperSelector'] = $this->getFromSystem('image_wrapper_selector'); //".product-image-container .product-image-wrapper";
        $optionArr['editActionSelector'] = $this->getFromSystem('edit_action_selector');// ".action.edit";
        return $this->jsonEncoder->encode($optionArr);
    }

    /**
     * @param bool $isCart
     * @return array
     */
    protected function getOptions($isCart = true)
    {
        if ($isCart){
            $items = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        }else{ //wishlist
            $wishlist = $this->_wishlistFactory->create()->loadByCustomerId($this->_customerSession->getCustomer()->getId());
            $items = $wishlist->getItemCollection();
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
                                    'url' => $this->helper->getImageUrl($value->getLayer(), $imageWidth, $imageHeight, $keepFrame),
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
        return $optionArr;
    }

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
}