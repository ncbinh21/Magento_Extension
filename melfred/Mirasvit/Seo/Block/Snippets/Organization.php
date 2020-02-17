<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Snippets;

use Mirasvit\Seo\Traits\SnippetsTrait;

class Organization extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\Information
     */
    protected $_storeInfo;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @param \Mirasvit\Seo\Model\Config                       $config
     * @param \Magento\Framework\Locale\ListsInterface         $localeLists
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mirasvit\Seo\Helper\Data                        $seoData
     * @param \Magento\Store\Model\Information                 $_storeInfo
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Store\Model\Information $storeInfo,
        array $data = []
    ) {
        $this->config = $config;
        $this->localeLists = $localeLists;
        $this->assetRepo = $context->getAssetRepository();
        $this->context = $context;
        $this->seoData = $seoData;
        $this->_storeInfo = $storeInfo;
        $this->store = $this->context->getStoreManager()->getStore();
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isOrganizationSnippets()
    {
        return $this->config->isOrganizationSnippetsEnabled($this->store->getStoreId());
    }

    /**
     * @return bool|string
     */
    public function getName()
    {
        if ($this->config->getNameOrganizationSnippets($this->store->getStoreId())) {
            $name = $this->config->getManualNameOrganizationSnippets($this->store->getStoreId());
        } else {
            if ($storeName = $this->_storeInfo->getStoreInformationObject($this->store)->getName()) {
                $name = $storeName;
            } else {
                $name = trim($this->context->getScopeConfig()->getValue('general/store_information/name'));
            }
        }

        if ($name) {
            $name = SnippetsTrait::prepareSnippet($name);
            return "\"name\" : \"$name\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getCountryAddress()
    {
        if ($this->config->getCountryAddressOrganizationSnippets($this->store->getStoreId())) {
            $countryAddress = $this->config->getManualCountryAddressOrganizationSnippets($this->store->getStoreId());
        } else {
            $countryAddress = trim($this->localeLists
                ->getCountryTranslation($this->context
                        ->getScopeConfig()
                        ->getValue('general/store_information/country_id')));
        }

        if ($countryAddress) {
            $countryAddress = SnippetsTrait::prepareSnippet($countryAddress);
            return "\"addressCountry\": \"$countryAddress\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getAddressLocality()
    {
        if ($this->config->getLocalityAddressOrganizationSnippets($this->store->getStoreId())) {
            $addressLocality = $this->config->getManualLocalityAddressOrganizationSnippets($this->store->getStoreId());
        } else {
            $addressLocality = trim($this->context->getScopeConfig()->getValue('general/store_information/city'));
        }

        if ($addressLocality) {
            $addressLocality = SnippetsTrait::prepareSnippet($addressLocality);
            return "\"addressLocality\": \"$addressLocality\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getPostalCode()
    {
        if ($this->config->getPostalCodeOrganizationSnippets($this->store->getStoreId())) {
            $postalCode = $this->config->getManualPostalCodeOrganizationSnippets($this->store->getStoreId());
        } else {
            $postalCode = trim($this->context->getScopeConfig()->getValue('general/store_information/postcode'));
        }

        if ($postalCode) {
            $postalCode = SnippetsTrait::prepareSnippet($postalCode);
            return "\"postalCode\": \"$postalCode\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getStreetAddress()
    {
        if ($this->config->getStreetAddressOrganizationSnippets($this->store->getStoreId())) {
            $streetAddress = $this->config->getManualStreetAddressOrganizationSnippets($this->store->getStoreId());
        } else {
            $streetAddress = trim($this->context->getScopeConfig()->getValue('general/store_information/street_line1').
                ' '.$this->context->getScopeConfig()->getValue('general/store_information/street_line2'));
        }

        if ($streetAddress) {
            $streetAddress = SnippetsTrait::prepareSnippet($streetAddress);
            return "\"streetAddress\": \"$streetAddress\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getTelephone()
    {
        if ($this->config->getTelephoneOrganizationSnippets($this->store->getStoreId())) {
            $telephone = $this->config->getManualTelephoneOrganizationSnippets($this->store->getStoreId());
        } else {
            $telephone = trim($this->context->getScopeConfig()->getValue('general/store_information/phone'));
        }

        if ($telephone) {
            $telephone = SnippetsTrait::prepareSnippet($telephone);
            return "\"telephone\": \"$telephone\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getFaxNumber()
    {
        if ($faxNumber = $this->config->getManualFaxnumberOrganizationSnippets($this->store->getStoreId())) {
            $faxNumber = SnippetsTrait::prepareSnippet($faxNumber);
            return "\"faxNumber\": \"$faxNumber\",";
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getEmail()
    {
        if ($this->config->getEmailOrganizationSnippets($this->store->getStoreId())) {
            $email = $this->config->getManualEmailOrganizationSnippets($this->store->getStoreId());
        } else {
            $email = trim($this->context->getScopeConfig()->getValue('trans_email/ident_general/email'));
        }

        if ($email) {
            $email = SnippetsTrait::prepareSnippet($email);
            return "\"email\" : \"$email\",";
        }

        return false;
    }

    /**
     * @param string $countryAddress
     * @param string $addressLocality
     * @param string $postalCode
     * @param string $streetAddress
     * @return string
     */
    public function preparePostalAddress($countryAddress, $addressLocality, $postalCode, $streetAddress)
    {
        $postalAddress = $countryAddress.$addressLocality.$postalCode.$streetAddress;
        if ($postalAddress && substr($postalAddress, -1) == ',') {
            $postalAddress = substr($postalAddress, 0, -1);
        }

        return $postalAddress;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->seoData->getLogoUrl();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->context->getUrlBuilder()->getBaseUrl();
    }
}
