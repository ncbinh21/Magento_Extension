<?php


namespace Forix\NetTerm\Model;

use Forix\NetTerm\Api\Data\NettermInterface;

class Netterm extends \Magento\Framework\Model\AbstractModel implements NettermInterface
{

    protected $_eventPrefix = 'forix_netterm_netterm';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Forix\NetTerm\Model\ResourceModel\Netterm::class);
    }

    /**
     * Get netterm_id
     * @return string
     */
    public function getNettermId()
    {
        return $this->getData(self::NETTERM_ID);
    }

    /**
     * Set netterm_id
     * @param string $nettermId
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setNettermId($nettermId)
    {
        return $this->setData(self::NETTERM_ID, $nettermId);
    }

    /**
     * Get business
     * @return string
     */
    public function getBusiness()
    {
        return $this->getData(self::BUSINESS);
    }

    /**
     * Set business
     * @param string $business
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setBusiness($business)
    {
        return $this->setData(self::BUSINESS, $business);
    }

    /**
     * Get type_business
     * @return string
     */
    public function getTypeBusiness()
    {
        return $this->getData(self::TYPE_BUSINESS);
    }

    /**
     * Set type_business
     * @param string $typeBusiness
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setTypeBusiness($typeBusiness)
    {
        return $this->setData(self::TYPE_BUSINESS, $typeBusiness);
    }

    /**
     * Get location_since
     * @return string
     */
    public function getLocationSince()
    {
        return $this->getData(self::LOCATION_SINCE);
    }

    /**
     * Set location_since
     * @param string $locationSince
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setLocationSince($locationSince)
    {
        return $this->setData(self::LOCATION_SINCE, $locationSince);
    }

    /**
     * Get year_established
     * @return string
     */
    public function getYearEstablished()
    {
        return $this->getData(self::YEAR_ESTABLISHED);
    }

    /**
     * Set year_established
     * @param string $yearEstablished
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setYearEstablished($yearEstablished)
    {
        return $this->setData(self::YEAR_ESTABLISHED, $yearEstablished);
    }

    /**
     * Get owners_officers
     * @return string
     */
    public function getOwnersOfficers()
    {
        return $this->getData(self::OWNERS_OFFICERS);
    }

    /**
     * Set owners_officers
     * @param string $ownersOfficers
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setOwnersOfficers($ownersOfficers)
    {
        return $this->setData(self::OWNERS_OFFICERS, $ownersOfficers);
    }

    /**
     * Get company_references
     * @return string
     */
    public function getCompanyReferences()
    {
        return $this->getData(self::COMPANY_REFERENCES);
    }

    /**
     * Set company_references
     * @param string $companyReferences
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setCompanyReferences($companyReferences)
    {
        return $this->setData(self::COMPANY_REFERENCES, $companyReferences);
    }

    /**
     * Get full_name
     * @return string
     */
    public function getFullName()
    {
        return $this->getData(self::FULL_NAME);
    }

    /**
     * Set full_name
     * @param string $fullName
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setFullName($fullName)
    {
        return $this->setData(self::FULL_NAME, $fullName);
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
     * Set is active
     * @param string $isActive
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get $isActive
     * @return string
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get date
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Set date
     * @param string $date
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }
}
