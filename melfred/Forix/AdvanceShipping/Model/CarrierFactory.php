<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 10/07/2018
 * Time: 15:52
 */

namespace Forix\AdvanceShipping\Model;

use Magento\Shipping\Model\Carrier;

/**
 * Class CarrierFactory
 * @package Forix\AdvanceShipping\Model
 */
class CarrierFactory extends \Magento\Shipping\Model\CarrierFactory
{

    /**
     * @return \Magento\Quote\Model\Quote
     */
    protected function getCurrentQuote()
    {
        /**
         * @var $registry \Magento\Framework\Registry
         */
        $registry = $this->_objectManager->get(\Magento\Framework\Registry::class);
        return $registry->registry('current_quote');
    }

    /**
     *
     * Check if total weight is in range of weight config with carrier code
     * if not in range will hidden in frontend
     * @param string $carrierCode
     * @return boolean
     */
    protected function isAvailable($carrierCode)
    {
        $quote = $this->getCurrentQuote();
        if (!$quote) {
            return true;
        }
        $methodNotes = $this->_scopeConfig->getValue('shipping/weight_config/config');
        if ($methodNotes) {
            $methodNotes = json_decode($methodNotes, true);
            if (is_array($methodNotes)) {
                foreach ($methodNotes as $methodNote) {
                    if ($methodNote['carrier_code'] === $carrierCode) {
                        list($min, $max) = explode(',', $methodNote['weight_config']);

                        $items = $quote->getAllItems();
                        $sumWeight = 0;
                        foreach ($items as $item) {
                            $sumWeight += $item->getWeight();
                        }
                        $sumWeight = (float)$sumWeight;
                        return ((float)$min <= $sumWeight && $sumWeight <= (float)$max);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Get carrier instance
     *
     * @param string $carrierCode
     * @return bool|Carrier\AbstractCarrier
     */
    public function get($carrierCode)
    {
        if ($this->isAvailable($carrierCode)) {
            return parent::get($carrierCode);
        }
        return false;
    }

    /**
     * @param string $carrierCode
     * @param null $storeId
     * @return bool|Carrier\AbstractCarrier
     */
    public function create($carrierCode, $storeId = null)
    {
        if ($this->isAvailable($carrierCode)) {
            return parent::create($carrierCode, $storeId);
        }
        return false;
    }
}