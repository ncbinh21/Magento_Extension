<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\AdvanceShipping\Rewrite\Magento\Ups\Model;


use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Ups\Helper\Config;
use Magento\Checkout\Model\Cart;

class Carrier extends \Magento\Ups\Model\Carrier
{
    /**
     * @var \Magento\Shipping\Model\Shipping
     */
    protected $_shipping;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * Carrier constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param Config $configHelper
     * @param \Magento\Shipping\Model\Shipping $shipping
     * @param Cart $cart
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        Config $configHelper,
        \Magento\Shipping\Model\Shipping $shipping,
        Cart $cart,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $xmlSecurity, $xmlElFactory, $rateFactory, $rateMethodFactory, $trackFactory, $trackErrorFactory, $trackStatusFactory, $regionFactory, $countryFactory, $currencyFactory, $directoryData, $stockRegistry, $localeFormat, $configHelper, $data);
        $this->_shipping = $shipping;
        $this->_cart = $cart;
    }

    public function setRequest(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        parent::setRequest($request);
        if ($request->getPackageCurrency()) {
            $this->_rawRequest->setCurrencyCode($request->getPackageCurrency()->getCurrencyCode());
        }
        return $this;
    }

    protected function _getQuotes($packages = [])
    {
        switch ($this->getConfigData('type')) {
            case 'UPS':
                return $this->_getCgiQuotes();
            case 'UPS_XML':
                return $this->_getXmlQuotes($packages);
            default:
                break;
        }
        return null;
    }

    public function collectRates(RateRequest $request)
    {
        $packages = $this->_shipping->composePackagesForCarrier($this, $request);
        $this->setRequest($request);
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }

        $this->setRequest($request);
        $this->_result = $this->_getQuotes($packages);
        $this->_updateFreeMethodQuote($request);

        return $this->getResult();

    }

    protected function _getXmlQuotes($packages = [])
    {
        $url = $this->getConfigData('gateway_xml_url');

        $this->setXMLAccessRequest();
        $xmlRequest = $this->_xmlAccessRequest;
        $debugData['accessRequest'] = $this->filterDebugData($xmlRequest);

        $rowRequest = $this->_rawRequest;
        if (self::USA_COUNTRY_ID == $rowRequest->getDestCountry()) {
            $destPostal = substr($rowRequest->getDestPostal(), 0, 5);
        } else {
            $destPostal = $rowRequest->getDestPostal();
        }
        $params = [
            'accept_UPS_license_agreement' => 'yes',
            '10_action' => $rowRequest->getAction(),
            '13_product' => $rowRequest->getProduct(),
            '14_origCountry' => $rowRequest->getOrigCountry(),
            '15_origPostal' => $rowRequest->getOrigPostal(),
            'origCity' => $rowRequest->getOrigCity(),
            'origRegionCode' => $rowRequest->getOrigRegionCode(),
            '19_destPostal' => $destPostal,
            '22_destCountry' => $rowRequest->getDestCountry(),
            'destRegionCode' => $rowRequest->getDestRegionCode(),
            '23_weight' => $rowRequest->getWeight(),
            '47_rate_chart' => $rowRequest->getPickup(),
            '48_container' => $rowRequest->getContainer(),
            '49_residential' => $rowRequest->getDestType(),
        ];

        if ($params['10_action'] == '4') {
            $params['10_action'] = 'Shop';
            $serviceCode = null;
        } else {
            $params['10_action'] = 'Rate';
            $serviceCode = $rowRequest->getProduct() ? $rowRequest->getProduct() : '';
        }
        $serviceDescription = $serviceCode ? $this->getShipmentByCode($serviceCode) : '';

        $xmlParams = <<<XMLRequest
<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang="en-US">
  <Request>
    <TransactionReference>
      <CustomerContext>Rating and Service</CustomerContext>
      <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
    <RequestAction>Rate</RequestAction>
    <RequestOption>{$params['10_action']}</RequestOption>
  </Request>
  <PickupType>
          <Code>{$params['47_rate_chart']['code']}</Code>
          <Description>{$params['47_rate_chart']['label']}</Description>
  </PickupType>

  <Shipment>
XMLRequest;

        if ($serviceCode !== null) {
            $xmlParams .= "<Service>" .
                "<Code>{$serviceCode}</Code>" .
                "<Description>{$serviceDescription}</Description>" .
                "</Service>";
        }

        $xmlParams .= <<<XMLRequest
      <Shipper>
XMLRequest;

        if ($this->getConfigFlag('negotiated_active') && ($shipper = $this->getConfigData('shipper_number'))) {
            $xmlParams .= "<ShipperNumber>{$shipper}</ShipperNumber>";
        }

        if ($rowRequest->getIsReturn()) {
            $shipperCity = '';
            $shipperPostalCode = $params['19_destPostal'];
            $shipperCountryCode = $params['22_destCountry'];
            $shipperStateProvince = $params['destRegionCode'];
        } else {
            $shipperCity = $params['origCity'];
            $shipperPostalCode = $params['15_origPostal'];
            $shipperCountryCode = $params['14_origCountry'];
            $shipperStateProvince = $params['origRegionCode'];
        }

        $xmlParams .= <<<XMLRequest
      <Address>
          <City>{$shipperCity}</City>
          <PostalCode>{$shipperPostalCode}</PostalCode>
          <CountryCode>{$shipperCountryCode}</CountryCode>
          <StateProvinceCode>{$shipperStateProvince}</StateProvinceCode>
      </Address>
    </Shipper>
    <ShipTo>
      <Address>
          <PostalCode>{$params['19_destPostal']}</PostalCode>
          <CountryCode>{$params['22_destCountry']}</CountryCode>
          <ResidentialAddress>{$params['49_residential']}</ResidentialAddress>
          <StateProvinceCode>{$params['destRegionCode']}</StateProvinceCode>
XMLRequest;

        if ($params['49_residential'] === '01') {
            $xmlParams .= "<ResidentialAddressIndicator>{$params['49_residential']}</ResidentialAddressIndicator>";
        }

        $xmlParams .= <<<XMLRequest
      </Address>
    </ShipTo>


    <ShipFrom>
      <Address>
          <PostalCode>{$params['15_origPostal']}</PostalCode>
          <CountryCode>{$params['14_origCountry']}</CountryCode>
          <StateProvinceCode>{$params['origRegionCode']}</StateProvinceCode>
      </Address>
    </ShipFrom>
XMLRequest;
        if (!empty($packages)) {
            $pId = 1;
            foreach ($packages as $pWeight => $pCount) {
                for ($i = 1; $i <= $pCount; $i++) {
                    $xmlParams .= <<<XMLRequest
    <Package id="{$pId}">
      <PackagingType><Code>{$params['48_container']}</Code></PackagingType>
      <PackageWeight>
         <UnitOfMeasurement><Code>{$rowRequest->getUnitMeasure()}</Code></UnitOfMeasurement>
        <Weight>{$pWeight}</Weight>
      </PackageWeight>
    </Package>
XMLRequest;
                    $pId++;
                }
            }
        } else {
            $xmlParams .= <<<XMLRequest
    <Package>
      <PackagingType><Code>{$params['48_container']}</Code></PackagingType>
      <PackageWeight>
         <UnitOfMeasurement><Code>{$rowRequest->getUnitMeasure()}</Code></UnitOfMeasurement>
        <Weight>{$params['23_weight']}</Weight>
      </PackageWeight>
    </Package>
XMLRequest;
        }

        if ($this->getConfigFlag('negotiated_active')) {
            $xmlParams .= "<RateInformation><NegotiatedRatesIndicator/></RateInformation>";
        }
        $xmlParams .= <<<XMLRequest
  </Shipment>
</RatingServiceSelectionRequest>
XMLRequest;
        $xmlRequest .= $xmlParams;

        $xmlResponse = null;
        // $xmlResponse = $this->_getCachedQuotes($xmlRequest);
        if ($xmlResponse === null) {
            $debugData['request'] = $xmlParams;
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (bool)$this->getConfigFlag('mode_xml'));
                $xmlResponse = curl_exec($ch);
                if ($xmlResponse !== false) {
                    $debugData['result'] = $xmlResponse;
                // $this->_setCachedQuotes($xmlRequest, $xmlResponse);
                } else {
                    $debugData['result'] = ['error' => curl_error($ch)];
                }
                curl_close($ch);
            } catch (\Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                $xmlResponse = '';
            }
            $this->_debug($debugData);
        }

        return $this->_parseXmlResponse($xmlResponse);
    }

    protected function _getPerorderPrice($cost, $handlingType, $handlingFee)
    {
        $insuredValue = $this->getConfigData('insured_value')?:0;
        return $cost + $handlingFee + (float)($this->_cart->getQuote()->getSubtotal() / 100 * (float)$insuredValue);
    }

    /**
     * Get final price for shipping method with handling fee per package
     *
     * @param float $cost
     * @param string $handlingType
     * @param float $handlingFee
     * @return float
     */
    protected function _getPerpackagePrice($cost, $handlingType, $handlingFee)
    {

        $insuredValue = $this->getConfigData('insured_value')?:0;
        if ($handlingType == self::HANDLING_TYPE_PERCENT) {
            return ($cost + $cost * $handlingFee / 100) + (float)(floor($cost/100) * (float)$insuredValue);
        }

        return ($cost + $handlingFee) + (float)(floor($cost/100) * (float)$insuredValue);
    }
}