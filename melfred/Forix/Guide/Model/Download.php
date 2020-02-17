<?php


namespace Forix\Guide\Model;

use Forix\Guide\Api\Data\DownloadInterface;

class Download extends \Magento\Framework\Model\AbstractModel implements DownloadInterface
{

    protected $_eventPrefix = 'forix_guide_download';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Forix\Guide\Model\ResourceModel\Download::class);
    }

    /**
     * Get download_id
     * @return string
     */
    public function getDownloadId()
    {
        return $this->getData(self::DOWNLOAD_ID);
    }

    /**
     * Set download_id
     * @param string $downloadId
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setDownloadId($downloadId)
    {
        return $this->setData(self::DOWNLOAD_ID, $downloadId);
    }

    /**
     * Get customer_id
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get company
     * @return string
     */
    public function getCompany()
    {
        return $this->getData(self::COMPANY);
    }

    /**
     * Set company
     * @param string $company
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
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
     * @return \Forix\Guide\Api\Data\DownloadInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }
}
