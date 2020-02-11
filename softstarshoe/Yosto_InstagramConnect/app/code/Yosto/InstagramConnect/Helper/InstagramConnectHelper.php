<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class InstagramConnectHelper
 * @package Yosto\InstagramConnect\Helper
 */
class InstagramConnectHelper extends AbstractHelper
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
     * @param $imageNumber
     * @return null
     */
    public function getAllInstagramMedia($imageNumber)
    {
        if ($this->getInstagramAccessToken()) {
            $url = 'https://api.instagram.com/v1/'
                . 'users/self/media/recent/?access_token='
                . $this->getInstagramAccessToken();
            $curl = curl_init();
            // Set some options
            curl_setopt_array(
                $curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    CURLOPT_SSL_VERIFYPEER => false
                )
            );
            // Send the request & save response to $resp
            $jsonData = curl_exec($curl);

            // Close request to clear up some resources
            curl_close($curl);
            $info = [];
            $returnInfor = [];
            if ($jsonData) {
                $jsonUserMedia = json_decode($jsonData);
                if ($jsonUserMedia->meta->code == 200) {
                    $listMedia = [];
                    foreach ($jsonUserMedia->data as $media) {
                        $listMedia[] = $media->images->standard_resolution->url;
                        $info[$media->images->standard_resolution->url] = array($media->likes->count, $media->comments->count);
                    }
                    if (count($listMedia) > $imageNumber) {
                        $returnInfor[] = array_slice($info, 0, $imageNumber);
                        return $returnInfor;
                    } else {
                        return $info;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $hashTag
     * @return array|null
     */
    public function getOnlyImageByTag($hashTag)
    {
        if ($this->getInstagramAccessToken()) {
            $url = 'https://api.instagram.com/v1/tags/'
                . $hashTag
                . '/media/recent/?access_token='
                . $this->getInstagramAccessToken();
            $curl = curl_init();
            // Set some options
            curl_setopt_array(
                $curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    CURLOPT_SSL_VERIFYPEER => false
                )
            );
            // Send the request & save response to $resp
            $jsonData = curl_exec($curl);

            // Close request to clear up some resources
            curl_close($curl);
            if ($jsonData) {
                $jsonUserMedia = json_decode($jsonData);
                if ($jsonUserMedia->meta->code == 200) {
                    $listMedia = [];
                    foreach ($jsonUserMedia->data as $media) {
                        $listMedia[] = $media->images->standard_resolution->url;
                    }
                    return $listMedia;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $hashTag
     * @param $imageNumber
     * @return null
     */
    public function getInstagramMediaByTagWithTag($hashTag, $imageNumber)
    {
        if ($this->getInstagramAccessToken()) {
            $url = 'https://api.instagram.com/v1/tags/'
                . $hashTag
                . '/media/recent/?access_token='
                . $this->getInstagramAccessToken();
            $curl = curl_init();
            // Set some options
            curl_setopt_array(
                $curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    CURLOPT_SSL_VERIFYPEER => false
                )
            );
            // Send the request & save response to $resp
            $jsonData = curl_exec($curl);

            // Close request to clear up some resources
            curl_close($curl);
            $info = [];
            $returnInfor = [];
            if ($jsonData) {
                $jsonUserMedia = json_decode($jsonData);
                if ($jsonUserMedia->meta->code == 200) {
                    $listMedia = [];
                    foreach ($jsonUserMedia->data as $media) {
                        $listMedia[] = $media->images->standard_resolution->url;
                        $info[$media->images->standard_resolution->url] = array($media->likes->count, $media->comments->count);
                    }
                    if (count($listMedia) > $imageNumber) {
                        $returnInfor[] = array_slice($info, 0, $imageNumber);
                        return $returnInfor;
                    } else {
                        return $info;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
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
     * @param $currentHashTag
     * @return null
     */
    public function getInstagramMediaByTag($currentHashTag)
    {
        if ($this->getInstagramAccessToken()) {
            $url = 'https://api.instagram.com/v1/tags/'
                . $currentHashTag
                . '/media/recent/?access_token='
                . $this->getInstagramAccessToken();
            $curl = curl_init();
            // Set some options
            curl_setopt_array(
                $curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    CURLOPT_SSL_VERIFYPEER => false
                )
            );
            // Send the request & save response to $resp
            $jsonData = curl_exec($curl);

            // Close request to clear up some resources
            curl_close($curl);
            $imageNumber = $this->getProductDetailImageNumber();

            $info = [];
            $returnInfor = [];
            if ($jsonData) {
                $jsonUserMedia = json_decode($jsonData);

                if ($jsonUserMedia->meta->code == 200) {
                    $listMedia = [];
                    foreach ($jsonUserMedia->data as $media) {
                        $listMedia[] = $media->images->standard_resolution->url;
                        $info[$media->images->standard_resolution->url] = array($media->likes->count, $media->comments->count);
                    }

                    if (count($listMedia) > $imageNumber) {
                        $returnInfor[] = array_slice($info, 0, $imageNumber);
                        return $returnInfor;
                    } else {
                        return $info;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
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
     * @param $followerNumber
     * @return null
     */
    public function getUserFollowed($followerNumber)
    {
        if ($this->getInstagramAccessToken()) {
            $url = 'https://api.instagram.com/v1/users/self/followed-by?access_token='
                . $this->getInstagramAccessToken();
            $curl = curl_init();
            // Set some options
            curl_setopt_array(
                $curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    CURLOPT_SSL_VERIFYPEER => false
                )
            );
            // Send the request & save response to $resp
            $jsonData = curl_exec($curl);

            // Close request to clear up some resources
            curl_close($curl);
            $info = [];
            $returnInfor = [];

            if ($jsonData) {
                $jsonFollowedUsers = json_decode($jsonData);
                if ($jsonFollowedUsers->meta->code == 200) {
                    foreach ($jsonFollowedUsers->data as $follower) {


                        $info[$follower->id] = array($follower->profile_picture, $follower->full_name, $follower->username);
                    }

                    if (count($info) > $followerNumber) {
                        $returnInfor[] = array_slice($info, 0, $followerNumber);
                        return $returnInfor;
                    } else {
                        return $info;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
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
}