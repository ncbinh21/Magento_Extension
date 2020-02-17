<?php


namespace Forix\NetTerm\Api\Data;

interface NettermInterface
{

    const COMPANY_REFERENCES = 'company_references';
    const TITLE = 'title';
    const NETTERM_ID = 'netterm_id';
    const DATE = 'date';
    const TYPE_BUSINESS = 'type_business';
    const LOCATION_SINCE = 'location_since';
    const BUSINESS = 'business';
    const OWNERS_OFFICERS = 'owners_officers';
    const FULL_NAME = 'full_name';
    const YEAR_ESTABLISHED = 'year_established';
    const IS_ACTIVE = 'is_active';

    /**
     * Get netterm_id
     * @return string|null
     */
    public function getNettermId();

    /**
     * Set netterm_id
     * @param string $nettermId
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setNettermId($nettermId);

    /**
     * Get business
     * @return string|null
     */
    public function getBusiness();

    /**
     * Set business
     * @param string $business
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setBusiness($business);

    /**
     * Get type_business
     * @return string|null
     */
    public function getTypeBusiness();

    /**
     * Set type_business
     * @param string $typeBusiness
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setTypeBusiness($typeBusiness);

    /**
     * Get location_since
     * @return string|null
     */
    public function getLocationSince();

    /**
     * Set location_since
     * @param string $locationSince
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setLocationSince($locationSince);

    /**
     * Get year_established
     * @return string|null
     */
    public function getYearEstablished();

    /**
     * Set year_established
     * @param string $yearEstablished
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setYearEstablished($yearEstablished);

    /**
     * Get owners_officers
     * @return string|null
     */
    public function getOwnersOfficers();

    /**
     * Set owners_officers
     * @param string $ownersOfficers
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setOwnersOfficers($ownersOfficers);

    /**
     * Get company_references
     * @return string|null
     */
    public function getCompanyReferences();

    /**
     * Set company_references
     * @param string $companyReferences
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setCompanyReferences($companyReferences);

    /**
     * Get full_name
     * @return string|null
     */
    public function getFullName();

    /**
     * Set full_name
     * @param string $fullName
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setFullName($fullName);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setTitle($title);

    /**
     * Get date
     * @return string|null
     */
    public function getDate();

    /**
     * Set date
     * @param string $date
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     */
    public function setDate($date);
}
