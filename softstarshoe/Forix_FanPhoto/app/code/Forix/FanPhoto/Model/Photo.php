<?php


namespace Forix\FanPhoto\Model;

use Forix\FanPhoto\Api\Data\PhotoInterface;

class Photo extends \Magento\Framework\Model\AbstractModel implements PhotoInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\FanPhoto\Model\ResourceModel\Photo');
    }

    /**
     * Get photo_id
     * @return string
     */
    public function getPhotoId()
    {
        return $this->getData(self::PHOTO_ID);
    }

    /**
     * Set photo_id
     * @param string $photoId
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setPhotoId($photoId)
    {
        return $this->setData(self::PHOTO_ID, $photoId);
    }

    /**
     * Get image_url
     * @return string
     */
    public function getImageUrl()
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * Set image_url
     * @param string $image_url
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setImageUrl($image_url)
    {
        return $this->setData(self::IMAGE_URL, $image_url);
    }

    /**
     * Get category_name
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getData(self::CATEGORY_NAME);
    }

    /**
     * Set category_name
     * @param string $category_name
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCategoryName($category_name)
    {
        return $this->setData(self::CATEGORY_NAME, $category_name);
    }

    /**
     * Get caption
     * @return string
     */
    public function getCaption()
    {
        return $this->getData(self::CAPTION);
    }

    /**
     * Set caption
     * @param string $caption
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCaption($caption)
    {
        return $this->setData(self::CAPTION, $caption);
    }

    /**
     * Get city
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get state
     * @return string
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * Set state
     * @param string $state
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * Get country
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * Set country
     * @param string $country
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * Get firstname
     * @return string
     */
    public function getFirstname()
    {
        return $this->getData(self::FIRSTNAME);
    }

    /**
     * Set firstname
     * @param string $firstname
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setFirstname($firstname)
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * Get lastname
     * @return string
     */
    public function getLastname()
    {
        return $this->getData(self::LASTNAME);
    }

    /**
     * Set lastname
     * @param string $lastname
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setLastname($lastname)
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get twitter
     * @return string
     */
    public function getTwitter()
    {
        return $this->getData(self::TWITTER);
    }

    /**
     * Set twitter
     * @param string $twitter
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setTwitter($twitter)
    {
        return $this->setData(self::TWITTER, $twitter);
    }

    /**
     * Get instagram
     * @return string
     */
    public function getInstagram()
    {
        return $this->getData(self::INSTAGRAM);
    }

    /**
     * Set instagram
     * @param string $instagram
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setInstagram($instagram)
    {
        return $this->setData(self::INSTAGRAM, $instagram);
    }
}
