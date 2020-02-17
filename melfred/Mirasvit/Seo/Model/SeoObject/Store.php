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



namespace Mirasvit\Seo\Model\SeoObject;

use Magento\Directory\Model\RegionFactory;

class Store extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

     /**
      * @var RegionFactory
      */
    protected $regionFactory;

    /**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterface
     */
    protected $countryInformation;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation,
        RegionFactory $regionFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->urlManager = $urlManager;
        $this->countryInformation = $countryInformation;
        $this->regionFactory = $regionFactory;
        $this->_data = $data;
        $this->_construct();
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $store = $this->storeManager->getStore();
        $this->setData($store->getData());

        $countryName = '';
        if ($this->getConfigValue('general/store_information/name', $store)) {
            $this->setName($this->getConfigValue('general/store_information/name', $store));
        }
        $this->setPhone($this->getConfigValue('general/store_information/phone', $store));
        $this->setEmail($this->getConfigValue('trans_email/ident_general/email', $store));
        $this->setUrl($this->urlManager->getBaseUrl());

        $streetLineOne = $this->getConfigValue('general/store_information/street_line1', $store);
        $this->setStreetLine1($streetLineOne);
        $streetLineTwo = $this->getConfigValue('general/store_information/street_line2', $store);
        $this->setStreetLineTwo($streetLineTwo);
        $city = $this->getConfigValue('general/store_information/city', $store);
        $this->setCity($city);
        $regionId = $this->getConfigValue('general/store_information/region_id', $store);
        $region = $this->regionFactory->create();
        $region->load($regionId);
        $regionCode = $region->getCode();
        $this->setRegionCode($regionCode);
        $postcode = $this->getConfigValue('general/store_information/postcode', $store);
        $this->setPostcode($postcode);

        if ($countryId = $this->getConfigValue('general/store_information/country_id', $store)) {
            try { // to catch "Requested country is not available." for some stores
                $country = $this->countryInformation->getCountryInfo($countryId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $country = false;
            }
            if ($country) {
                $countryName = $country->getFullNameLocale();
                $this->setCountryName($countryName);
            }
        }


        $this->setStoreAddress($streetLineOne, $streetLineTwo, $city, $regionCode, $postcode, $countryName);
    }

    /**
     * @param string $streetLineOne
     * @param string $streetLineTwo
     * @param string $city
     * @param string $regionCode
     * @param string $postcode
     * @param string  $countryName
     * @return void
     * @SuppressWarnings(PMD.CyclomaticComplexity)
     */
    protected function setStoreAddress($streetLineOne, $streetLineTwo, $city, $regionCode, $postcode, $countryName)
    {
        $storeAdress = '';
        $separator = '<br/>';

        $storeAdressData = [
                    'street_line1' => $streetLineOne,
                    'street_line2' => $streetLineTwo,
                    'city' => $city,
                    'region_code' => $regionCode,
                    'postcode' => $postcode,
                    'country_name' => $countryName,
        ];

        foreach ($storeAdressData as $key => $adress) {
            switch ($key) {
                case 'street_line1':
                case 'street_line2':
                case 'country_name':
                case 'postcode':
                    if ($adress) {
                        $storeAdress .= $adress . $separator;
                    }
                    break;
                case 'city':
                    if ($adress && ($storeAdressData['region_code'] || $storeAdressData['postcode'])) {
                        $storeAdress .=  $adress . ', ';
                    } elseif ($adress) {
                        $storeAdress .= $adress . $separator;
                    }
                    break;
                case 'region_code':
                    if ($adress) {
                        $storeAdress .= $adress . ' ';
                    }
            };
        }

        $this->setAddress($storeAdress);
    }

    /**
     * @param string $key
     * @param Magento\Store\Model\Store\Interceptor &$store
     * @return string
     */
    protected function getConfigValue($key, &$store)
    {
        return $this->scopeConfig->getValue(
            $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
