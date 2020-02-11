<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Block\Instagram;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Yosto\InstagramConnect\Helper\InstagramClient;
use Magento\Framework\Registry;

/**
 * Class ImageSlider
 * @package Yosto\InstagramConnect\Block\Instagram
 */
class ImageSlider extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var InstagramClient
     */
    protected $_instagramConnectHelper;

    /**
     * @var string
     */
    protected $_currentRoute;

    /**
     * @var Registry
     */
    protected $_registry;


    public function __construct(
        Context $context,
        array $data = [],
        InstagramClient $instagramConnectHelper,
        Registry $registry
    )
    {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->_currentRoute = $this->getCurrentRoute();
    }

    /**
     * @return string
     */
    public function getCurrentRoute()
    {
        return $this->getRequest()->getModuleName()
        . '/'
        . $this->getRequest()->getControllerName();
    }

    /**
     * @return bool
     */
    public function isDisplayOnCatalog()
    {
        if ($this->_currentRoute == 'catalog/product' && $this->_instagramConnectHelper->getIsProductDisplayConfig() == 1) {
            return true;
        } else {
            return false;
        }

    }

    public function isCatalogPage()
    {
        if ($this->_currentRoute == 'catalog/product') {
            return true;
        }
        return false;
    }

    /**
     * @param $imageNumber
     * @return array|null
     */
    public function getAllInstagramMedia($imageNumber)
    {
        return $this->_instagramConnectHelper
            ->getUserMedia($id = "self" , $imageNumber)->data;
    }

    /**
     * @return array|null
     */
    public function getInstagramMediaByTag()
    {
        $currentHashTag = $this->getCurrentHashTag();
        if ($currentHashTag) {
            $result = $this->_instagramConnectHelper
                ->getTagMedia($currentHashTag);
            return $result->data;
        } else {
            return null;
        }

    }

    public function getInstagramMediaByTagOnProductPage()
    {
        $currentHashTag = $this->getCurrentHashTag();
        $limitImage = $this->_instagramConnectHelper->getProductDetailImageNumber();
        if ($currentHashTag) {
            $result = $this->_instagramConnectHelper
                ->getTagMedia($currentHashTag, $limitImage);
            return $result->data;
        } else {
            return null;
        }
    }



    /**
     * @return array|null
     */
    public function getInstagramMediaByTagWithTag($hashTag,$imageNumber)
    {
        $result = $this->_instagramConnectHelper
                ->getTagMedia($hashTag, $imageNumber);
        return $result->data;

    }

    /**
     * @return mixed
     */
    public function getCurrentHashTag()
    {
        if ($currentProduct = $this->getCurrentProduct()) {
            return $currentProduct->getInstagramHashTag();
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return mixed
     */
    public function getIsDisplayLikesComments()
    {
        return $this->_instagramConnectHelper->getIsDisplayLikesComments();
    }



}