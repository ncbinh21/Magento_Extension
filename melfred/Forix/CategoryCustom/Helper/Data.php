<?php

namespace Forix\CategoryCustom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    protected $_storeManager;
    protected $_categoryFactory;
    protected $_categoryRepository;
    protected $_urlBuilderHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Shopby\Helper\UrlBuilder $urlBuilderHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_urlBuilderHelper = $urlBuilderHelper;
    }

    /**
     * This function use only for ground condition category
     * @param $filer \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     * @param $value
     * @return string
     */
    public function getBuildUrl($filer, $value)
    {
        $query = $this->_urlBuilderHelper->buildQuery($filer, $value);

        if($category = $filer->getLayer()->getCurrentCategory()){
            $query['cat'] = $category->getId();
        }
        $query['shopbyAjax'] = null;
        $query['_'] = null;

        $params = ['_current' => true, '_use_rewrite' => true, '_query' => $query];
        $params['price'] = null;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    public function getCategoryThumbUrl($category, $resize = false, $typeArr = false)
    {
        $url = false;
        if ($typeArr) {
            $image = $category['img'];
        } else {
            $image = $category->getIconImage();
        }

        if ($resize) {
            $path = 'resized/316/';
        } else {
            $path = 'catalog/category/';
        }

        if (isset($image) && $image != "") {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . $path . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }

        return $url;
    }

    public function getSystemConfig($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    public function getCategory($id)
    {
        if ($id) {
            $category = $this->_categoryRepository->get($id);
            return $category;
        }
        return null;
    }

    public function builderUrlFilter($param)
    {
        $params = ['_current' => true, '_use_rewrite' => true, '_query' => $param];
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    public function getRequestAttribute($code)
    {

        if (!$value = $this->_getRequest()->getParam($code)) {
            if (isset($this->_getRequest()->getParam('amshopby')[$code])) {
                $value = $this->_getRequest()->getParam('amshopby')[$code];
            }
        }

//        if (isset($this->_getRequest()->getParam('amshopby')[$code]) ) {
//            $value = $this->_getRequest()->getParam('amshopby')[$code][0];
//        } else {
//            $value = $this->_getRequest()->getParam($code);
//        }
        return $value;
    }

    public function getSingleCategory($id)
    {
        return $this->_categoryRepository->get($id, $this->_storeManager->getStore()->getId());
    }
}