<?php


namespace Forix\FanPhoto\Api\Data;

interface PhotoInterface
{

    const CAPTION = 'caption';
    const COUNTRY = 'country';
    const STATE = 'state';
    const TWITTER = 'twitter';
    const EMAIL = 'email';
    const IMAGE_URL = 'image_url';
    const LASTNAME = 'lastname';
    const PHOTO_ID = 'photo_id';
    const CATEGORY_NAME = 'category_name';
    const CITY = 'city';
    const INSTAGRAM = 'instagram';
    const FIRSTNAME = 'firstname';


    /**
     * Get photo_id
     * @return string|null
     */
    public function getPhotoId();

    /**
     * Set photo_id
     * @param string $photo_id
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setPhotoId($photoId);

    /**
     * Get image_url
     * @return string|null
     */
    public function getImageUrl();

    /**
     * Set image_url
     * @param string $image_url
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setImageUrl($image_url);

    /**
     * Get category_name
     * @return string|null
     */
    public function getCategoryName();

    /**
     * Set category_name
     * @param string $category_name
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCategoryName($category_name);

    /**
     * Get caption
     * @return string|null
     */
    public function getCaption();

    /**
     * Set caption
     * @param string $caption
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCaption($caption);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCity($city);

    /**
     * Get state
     * @return string|null
     */
    public function getState();

    /**
     * Set state
     * @param string $state
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setState($state);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setCountry($country);

    /**
     * Get firstname
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set firstname
     * @param string $firstname
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setFirstname($firstname);

    /**
     * Get lastname
     * @return string|null
     */
    public function getLastname();

    /**
     * Set lastname
     * @param string $lastname
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setLastname($lastname);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setEmail($email);

    /**
     * Get twitter
     * @return string|null
     */
    public function getTwitter();

    /**
     * Set twitter
     * @param string $twitter
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setTwitter($twitter);

    /**
     * Get instagram
     * @return string|null
     */
    public function getInstagram();

    /**
     * Set instagram
     * @param string $instagram
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     */
    public function setInstagram($instagram);
}
