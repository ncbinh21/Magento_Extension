<?php


namespace Forix\Distributor\Api\Data;

interface ZipcodeInterface
{

    const DISTRIBUTOR_ID = 'distributor_id';
    const ZIPCODE = 'zipcode';
    const CITY = 'city';
    const COUNTRY = 'country';
    const ZIPCODE_ID = 'zipcode_id';

    /**
     * Get zipcode_id
     * @return string|null
     */
    public function getZipcodeId();

    /**
     * Set zipcode_id
     * @param string $zipcodeId
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setZipcodeId($zipcodeId);

    /**
     * Get distributor_id
     * @return string|null
     */
    public function getDistributorId();

    /**
     * Set distributor_id
     * @param string $distributorId
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setDistributorId($distributorId);

    /**
     * Get zipcode
     * @return string|null
     */
    public function getZipcode();

    /**
     * Set zipcode
     * @param string $zipcode
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setZipcode($zipcode);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setCity($city);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setCountry($country);
}
