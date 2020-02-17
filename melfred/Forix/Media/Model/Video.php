<?php


namespace Forix\Media\Model;

use Forix\Media\Api\Data\VideoInterface;

class Video extends \Magento\Framework\Model\AbstractModel implements VideoInterface
{

    protected $_eventPrefix = 'forix_media_video';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\Media\Model\ResourceModel\Video');
    }

    /**
     * Get video_id
     * @return string
     */
    public function getVideoId()
    {
        return $this->getData(self::VIDEO_ID);
    }

    /**
     * Set video_id
     * @param string $videoId
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setVideoId($videoId)
    {
        return $this->setData(self::VIDEO_ID, $videoId);
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get product_url
     * @return string
     */
    public function getProductUrl()
    {
        return $this->getData(self::PRODUCT_URL);
    }

    /**
     * Set product_url
     * @param string $productUrl
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setProductUrl($productUrl)
    {
        return $this->setData(self::PRODUCT_URL, $productUrl);
    }

    /**
     * Get media_url
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->getData(self::MEDIA_URL);
    }

    /**
     * Set media_url
     * @param string $mediaUrl
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setMediaUrl($mediaUrl)
    {
        return $this->setData(self::MEDIA_URL, $mediaUrl);
    }
}
