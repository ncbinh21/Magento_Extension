<?php


namespace Forix\Media\Api\Data;

interface VideoInterface
{

    const DESCRIPTION = 'description';
    const MEDIA_URL = 'media_url';
    const TITLE = 'title';
    const VIDEO_ID = 'video_id';
    const PRODUCT_URL = 'product_url';


    /**
     * Get video_id
     * @return string|null
     */
    public function getVideoId();

    /**
     * Set video_id
     * @param string $videoId
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setVideoId($videoId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setTitle($title);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setDescription($description);

    /**
     * Get product_url
     * @return string|null
     */
    public function getProductUrl();

    /**
     * Set product_url
     * @param string $productUrl
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setProductUrl($productUrl);

    /**
     * Get media_url
     * @return string|null
     */
    public function getMediaUrl();

    /**
     * Set media_url
     * @param string $mediaUrl
     * @return \Forix\Media\Api\Data\VideoInterface
     */
    public function setMediaUrl($mediaUrl);
}
