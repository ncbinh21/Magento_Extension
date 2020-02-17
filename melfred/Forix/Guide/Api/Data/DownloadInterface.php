<?php


namespace Forix\Guide\Api\Data;

interface DownloadInterface
{

    const EMAIL = 'email';
    const DOWNLOAD_ID = 'download_id';
    const CUSTOMER_ID = 'customer_id';
    const COMPANY = 'company';
    const NAME = 'name';

    /**
     * Get download_id
     * @return string|null
     */
    public function getDownloadId();

    /**
     * Set download_id
     * @param string $downloadId
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setDownloadId($downloadId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setName($name);

    /**
     * Get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setCompany($company);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setEmail($email);
}
