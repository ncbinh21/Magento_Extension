<?php
/**
 * Created by PhpStorm.
 * User: nghiata
 * Date: 12/11/2017
 * Time: 22:22
 */

namespace Yosto\InstagramConnect\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(Context $context,StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }


    /**
     * @return mixed
     */
    public function getInstagramClientIdConfig()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_connect_config/instagram_client_id',
                $this->_storeScope
            );
    }

    /**
     * @return mixed
     */
    public function getInstagramClientSecretConfig()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_connect_config/instagram_client_secret',
                $this->_storeScope
            );
    }

    /**
     * @return mixed
     */
    public function getIsIndexDisplayConfig()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_display_config/instagram_display_index_page',
                $this->_storeScope
            );
    }

    /**
     * @return mixed
     */
    public function getIsProductDisplayConfig()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_display_config/instagram_display_product_page',
                $this->_storeScope
            );
    }

    /**
     * @return mixed
     */
    public function getInstagramAccessToken()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_connect_config/instagram_access_token',
                $this->_storeScope
            );
    }
    /**
     * @return mixed
     */
    public function getIsDisplayLikesComments()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_display_config/instagram_display_likes_comments',
                $this->_storeScope
            );
    }

    /**
     * @return mixed
     */
    public function getProductDetailImageNumber()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_display_config/instagram_product_image_number',
                $this->_storeScope
            );
    }


    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @param $_productId
     * @return string
     */
    public function getProductDetailPageUrl($_productId)
    {
        return $this->getBaseUrl() . 'quickshop/index/view/id/' . $_productId;
    }

    /**
     * @return string
     */
    public function getShoppingPageUrl()
    {
        return $this->getBaseUrl() . 'instagram-shopping-page';
    }

    /**
     * @return mixed
     */
    public function getShoppingPageLinkMenu()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_shopping_page_config/shopping_page_enable_link_navigation',
                $this->_storeScope
            );
    }

    /**
     * @return mixed
     */
    public function getShoppingPageLinkLabel()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_shopping_page_config/shopping_page_enable_link_label',
                $this->_storeScope
            );
    }
    /**
     * @return mixed
     */
    public function getShoppingPageTemplate()
    {
        return $this
            ->scopeConfig
            ->getValue(
                'yosto_instagram_connect/instagram_shopping_page_config/shopping_page_template',
                $this->_storeScope
            );
    }

}