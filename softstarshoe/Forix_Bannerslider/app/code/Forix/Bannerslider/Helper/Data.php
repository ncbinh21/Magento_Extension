<?php

/**
 * Forix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Forix.com license that is
 * available through the world-wide-web at this URL:
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Forix
 * @package     Forix_Bannerslider
 * @copyright   Copyright (c) 2012 Forix (http://www.forixwebdesign.com/)
 * @license
 */

namespace Forix\Bannerslider\Helper;

use Forix\Bannerslider\Model\Slider;

/**
 * Helper Data
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * category collection factory.
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * page model factory.
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;
    
    /**
     * scope config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * [__construct description].
     *
     * @param \Magento\Framework\App\Helper\Context                      $context              [description]
     * @param \Magento\Directory\Helper\Data                             $directoryData        [description]
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection       $countryCollection    [description]
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                 $storeManager         [description]
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->_storeManager = $storeManager;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_pageFactory = $pageFactory;
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * get Base Url Media.
     *
     * @param string $path   [description]
     * @param bool   $secure [description]
     *
     * @return string [description]
     */
    public function getBaseUrlMedia($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, $secure) . $path;
    }

    /**
     * get categories array.
     *
     * @return array
     */
    public function getCategoriesArray()
    {
        $categoriesArray = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('path', 'asc')
            ->load()
            ->toArray();

        $categories = array();
        foreach ($categoriesArray as $categoryId => $category) {
            if (isset($category['name']) && isset($category['level'])) {
                $categories[] = array(
                    'label' => $category['name'],
                    'level' => $category['level'],
                    'value' => $categoryId,
                );
            }
        }

        return $categories;
    }

    public function getCmsPagesArray()
    {
        $result = array(
            array(
                'label' => 'Please Choose CMS Pages',
                'value' => ''
            )
        );
        $cmsPageCollection = $this->_pageFactory->create()->getCollection()
                                ->addFieldToFilter('is_active', 1)
                                ->addFieldToFilter('identifier', array('neq' => 'home'));
        if ($cmsPageCollection->getSize()) {
            foreach ($cmsPageCollection as $cmsPage) {
                $result[] = array(
                    'label' => $cmsPage->getTitle(),
                    'value' => $cmsPage->getId()
                );
            }
        }

        return $result;
    }
    
    /**
     * get Slider Banner Url
     * @return string
     */
    public function getSliderBannerUrl()
    {
        return $this->_backendUrl->getUrl('*/*/banners', ['_current' => true]);
    }

    /**
     * get Backend Url
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getBackendUrl($route = '', $params = ['_current' => true])
    {
        return $this->_backendUrl->getUrl($route, $params);
    }

    /**
     * getSliderModeAvailable
     * @return array
     */
    public function getSliderModeAvailable()
    {
        return [
            Slider::STYLESLIDE_EVOLUTION_ONE => 'Slider Evolution Default',
            Slider::STYLESLIDE_EVOLUTION_TWO => 'Slider Evolution Caborno',
            Slider::STYLESLIDE_EVOLUTION_THREE => 'Slider Evolution Minimalist',
            Slider::STYLESLIDE_EVOLUTION_FOUR => 'Slider Evolution Fresh',
            Slider::STYLESLIDE_POPUP => 'Pop up on Home page',
            Slider::STYLESLIDE_SPECIAL_NOTE => 'Note displayed on all pages',
            Slider::STYLESLIDE_FLEXSLIDER_ONE => 'Slider',
            Slider::STYLESLIDE_FLEXSLIDER_TWO => 'FlexSlider 2',
            Slider::STYLESLIDE_FLEXSLIDER_THREE => 'FlexSlider 3',
            Slider::STYLESLIDE_FLEXSLIDER_THREE => 'FlexSlider 4',
            Slider::STYLESLIDE_CUSTOM => 'Custom',
            Slider::STYLESLIDE_BANNER => 'Banner',
        ];
    }

    /**
     *  get Style Slider
     * @return array
     */
    public function getStyleSlider()
    {
        return [
            [
                'label' => __('Select mode...'),
                'value' => '',
            ],
            [
                'label' => __('Slider'),
                'value' => Slider::STYLESLIDE_FLEXSLIDER_ONE
            ],
            [
                'label' => __('Banner'),
                'value' => Slider::STYLESLIDE_BANNER
            ]
        ];
    }

    /**
     * get Animation A
     * @return array
     */
    public function getAnimationA()
    {
        return [
            [
                'label' => __('Fade'),
                'value' => 'fade',
            ],
            [
                'label' => __('Square'),
                'value' => 'squarerandom',
            ],
            [
                'label' => __('Bar'),
                'value' => 'bar',
            ],
            [
                'label' => __('Rain'),
                'value' => 'rain',
            ],
        ];
    }

    /**
     * get Animation B
     * @return array
     */
    public function getAnimationB()
    {
        return [
            [
                'label' => __('Slide'),
                'value' => 'slide',
            ],
            [
                'label' => __('Fade'),
                'value' => 'fade',
            ],
        ];
    }

    /**
     * get Option Color
     * @return array
     */
    public function getOptionColor()
    {
        return [
            ['label' => __('Yellow'), 'value' => '#f7d700'],
            ['label' => __('Red'), 'value' => '#dd0707'],
            ['label' => __('Orange'), 'value' => '#ee5f00'],
            ['label' => __('Green'), 'value' => '#83ba00'],
            ['label' => __('Blue'), 'value' => '#23b8ff'],
            ['label' => __('Gray'), 'value' => '#999'],
        ];
    }

    /**
     * get Block Ids To Options Array
     * @return array
     */
    public function getBlockIdsToOptionsArray()
    {
        return [
            [
                'label' => __('------- Please choose position -------'),
                'value' => '',
            ],
            [
                'label' => __('Popular positions'),
                'value' => [
                    ['value' => 'cms-page-content-top', 'label' => __('Homepage-Content-Top')],
                ],
            ],
            [
                'label' => __('CMS Page'),
                'value' => [
                    ['value' => 'cms-view-content-top', 'label' => __('CMS-Page-Content-Top')],
                    ['value' => 'cms-contact-us-image', 'label' => __('CMS - Contact Us')],
                    ['value' => 'cms-about-us-image', 'label' => __('CMS - About Us')],
                    ['value' => 'cms-order-return-page', 'label' => __('CMS - Order & Return')],
                    ['value' => 'cms-company-register-page', 'label' => __('CMS - Company Create Page')],
                    ['value' => 'cms-blog-bannerslider', 'label' => __('CMS - Blog Banner/Slider')],
                ],
            ],
            [
                'label' => __('Promotion Page'),
                'value' => [
                    ['value' => 'contest-top-image', 'label' => __('Contest Image')],
                    ['value' => 'catalogrequest-top-image', 'label' => __('Catalog Request')],
                    ['value' => 'salesrep-top-image', 'label' => __('Sales Rep Contact')],
                    ['value' => 'chembio-top-image', 'label' => __('Chembio Landing Page')],
                ],
            ],
        ];
    }

    /**
     * get Available Positions
     * @return array
     */
    public function getAvailablePositions()
    {
        return [
            'cms-page-content-top' => __('Homepage content top'),
            'cms-view-content-top' => __('CMS-Page Content Top'),
            'cms-contact-us-image' => __('CMS Contact Us'),
            'cms-about-us-image' => __('CMS About Us'),
            'cms-order-return-page' => __('CMS Order & Return'),
            'cms-company-register-page' => __('CMS - Company Create Page'),
            'cms-blog-bannerslider' => __('CMS - Blog Banner/Slider'),
            'contest-top-image' => __('Contest Image'),
            'catalogrequest-top-image' => __('Catalog Request'),
            'salesrep-top-image' => __('Sales Rep Contact'),
            'chembio-top-image' => __('Chembio Landing Page'),
        ];
    }

    /**
     * get list slider for preview.
     *
     * @return []
     */
    public function getCoreSlider()
    {
        return [
            [
                'label' => __('Slider Evolution Default'),
                'value' => Slider::STYLESLIDE_EVOLUTION_ONE,
            ],
            [
                'label' => __('Slider Evolution Caborno'),
                'value' => Slider::STYLESLIDE_EVOLUTION_TWO,
            ],
            [
                'label' => __('Slider Evolution Minimalist'),
                'value' => Slider::STYLESLIDE_EVOLUTION_THREE,
            ],
            [
                'label' => __('Slider Evolution Fresh'),
                'value' => Slider::STYLESLIDE_EVOLUTION_FOUR,
            ],
            [
                'label' => __('Note display on all pages'),
                'value' => Slider::STYLESLIDE_SPECIAL_NOTE,
            ],
            [
                'label' => __('Slider'),
                'value' => Slider::STYLESLIDE_FLEXSLIDER_ONE,
            ],
            [
                'label' => __('FlexSlider 2'),
                'value' => Slider::STYLESLIDE_FLEXSLIDER_TWO,
            ],
            [
                'label' => __('FlexSlider 3'),
                'value' => Slider::STYLESLIDE_FLEXSLIDER_THREE,
            ],
            [
                'label' => __('FlexSlider 4'),
                'value' => Slider::STYLESLIDE_FLEXSLIDER_FOUR,
            ],
        ];
    }
}
