<?php

namespace Forix\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
	private $postCodeUS;
	protected $cache;
	protected $customerFactory;
	protected $customerOrderData;
	protected $paymentHelper;
    const ZCODECACHETAG = 'forix_custom_checkout_general_postcode';

	public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Forix\CustomerOrder\Helper\Data $customerOrderData,
        \Forix\Payment\Helper\PaymentHelper $paymentHelper,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\App\CacheInterface $cache
	) {
	    $this->paymentHelper = $paymentHelper;
	    $this->customerOrderData = $customerOrderData;
	    $this->customerFactory = $customerFactory;
		$this->_moduleReader = $moduleReader;
        $this->cache = $cache;
		parent::__construct($context);
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function getConfigValue($value)
	{
		return $this->scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getConfigPostCode()
	{
	    if (is_null($this->postCodeUS)) {
	        $zCodeUsId = self::ZCODECACHETAG.'_zipcode_us';
	        $zCodeFullId = self::ZCODECACHETAG.'_zipcode_full';
            $this->postCodeUS = [
                'zipcode_us'    =>  $this->cache->load($zCodeUsId),
                'zipcode_full'  =>  $this->cache->load($zCodeFullId)
            ];
            if (false === $this->postCodeUS['zipcode_us'] || false === $this->postCodeUS['zipcode_full']) {
                $zipCodeConfig = $this->getConfigValue('forix_custom_checkout/general/postcode');
                $zipCodes = explode(PHP_EOL, $zipCodeConfig);
                $aZips = array();
                $aZipCitys = array();
                foreach ($zipCodes as $item) {
                    if (trim($item) == '') continue;
                    $key_value = explode(',', trim($item));
                    $aZipCitys[$key_value[0]] = array($key_value[1], $key_value[2]);
                    $aZips[] = $key_value[0];
                }

                $this->postCodeUS = [
                    'zipcode_us' => \Zend_Json_Encoder::encode($aZips),
                    'zipcode_full' => \Zend_Json_Encoder::encode($aZipCitys),
                ];
                $this->cache->save($this->postCodeUS['zipcode_us'], $zCodeUsId, [], false);
                $this->cache->save($this->postCodeUS['zipcode_full'], $zCodeFullId, [], false);
            }
		}
		return $this->postCodeUS;
	}

	public function checkDistributorOrNotZone($order)
    {
        $billingAddress = $order->getBillingAddress();
        $postCode = $billingAddress->getPostcode();
        if ($customerId = $order->getCustomerId()) {
            $customer = $this->customerFactory->create()->load($customerId);
            if($isDistributor = $this->customerOrderData->isDistributor($customer)) {
                return true;
            } else if($postCode && !$this->paymentHelper->isMatchDistributor($postCode)) {
                return true;
            }
        } else {
            if($postCode && !$this->paymentHelper->isMatchDistributor($postCode)) {
                return true;
            }
        }
        return false;
    }

	public function getConfigTemplatePayment()
	{
		return $this->getConfigValue('forix_custom_checkout/payment/tab');
	}

	public function arrangeAttributeOption($options=[], $label="")
	{
		$rigModel = [];
		if (isset($options) && !empty($options)){
			foreach ($options as $key => $value) {
				if ($value['label'] == __("Your Rig Model")){
					$rigModel = $options[$key];
					unset($options[$key]);
					break;
				}
			}
		}
		if($rigModel) {
			array_unshift($options, $rigModel);
		}
		return $options;
//		if (isset($options) && !empty($options))
//		{
//			$keys = array_keys($options);
//			$last = end($keys);
//			$rigArr = $options[$last];
//			if ($options[$last]["label"] == __("Your Rig Model"))
//			{
//				unset($options[$last]);
//				if ($label!="")
//				{
//					$rigArr["label"] = $label;
//				}
//				array_unshift($options, $rigArr);
//			}
//		}
//		return $options;
	}

}