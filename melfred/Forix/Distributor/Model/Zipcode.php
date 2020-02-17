<?php


namespace Forix\Distributor\Model;

use Forix\Distributor\Api\Data\ZipcodeInterface;

class Zipcode extends \Magento\Framework\Model\AbstractModel implements ZipcodeInterface
{

    protected $_eventPrefix = 'forix_distributor_zipcode';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Forix\Distributor\Model\ResourceModel\Zipcode::class);
    }

    /**
     * Get zipcode_id
     * @return string
     */
    public function getZipcodeId()
    {
        return $this->getData(self::ZIPCODE_ID);
    }

    /**
     * Set zipcode_id
     * @param string $zipcodeId
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setZipcodeId($zipcodeId)
    {
        return $this->setData(self::ZIPCODE_ID, $zipcodeId);
    }

    /**
     * Get distributor_id
     * @return string
     */
    public function getDistributorId()
    {
        return $this->getData(self::DISTRIBUTOR_ID);
    }

    /**
     * Set distributor_id
     * @param string $distributorId
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setDistributorId($distributorId)
    {
        return $this->setData(self::DISTRIBUTOR_ID, $distributorId);
    }

    /**
     * Get zipcode
     * @return string
     */
    public function getZipcode()
    {
        return $this->getData(self::ZIPCODE);
    }

    /**
     * Set zipcode
     * @param string $zipcode
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setZipcode($zipcode)
    {
        return $this->setData(self::ZIPCODE, $zipcode);
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
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
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
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }
}
