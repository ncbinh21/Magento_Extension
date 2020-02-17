<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 09/07/2018
 * Time: 15:44
 */

namespace Forix\AdvanceShipping\Model\Carrier;


class Distributor extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var string
     */
    protected $_code = 'distributor';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect and get rates
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Framework\DataObject|bool|null
     * @api
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        //$this->_updateHeavyMethodQuote($request);

        //if ($request->getHeavyShipping()) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->getCarrierCode());
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->getCarrierCode());
            $method->setMethodTitle($this->getConfigData('name'));

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        //}

        return $result;
    }


    /**
     * Allows free shipping when all product items have free shipping (promotions etc.)
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return void
     */
    protected function _updateHeavyMethodQuote($request)
    {
    	$heavyWeight = $this->_scopeConfig->getValue("forix_catalog/heavy/weight");
    	$isHeavyCart = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; $i++) {
            if ($items[$i]->getProduct() instanceof \Magento\Catalog\Model\Product) {
            	$productWeight = $items[$i]->getProduct()->getData("weight");
                if ($productWeight > $heavyWeight) {
                    $isHeavyCart = true;
                    break;
                }
            }
        }
        $request->setHeavyShipping($isHeavyCart);
    }


    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [$this->getCarrierCode() => $this->getConfigData('name')];
    }
}