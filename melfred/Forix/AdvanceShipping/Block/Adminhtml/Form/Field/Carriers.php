<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborazall
 * Date: 06/07/2018
 * Time: 14:38
 */

namespace Forix\AdvanceShipping\Block\Adminhtml\Form\Field;

class Carriers extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * methodList
     *
     * @var array
     */

    protected $shipConfig;

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Shipping\Model\Config $shipconfig,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->shipConfig = $shipconfig;
        $this->scopeConfig = $context->getScopeConfig();
    }
    /**
     * Returns countries array
     *
     * @return array
     */
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $activeCarriers = $this->shipConfig->getActiveCarriers();
            $options = array();
            /**
             * @var $carrierModel \Magento\Shipping\Model\Carrier\AbstractCarrier
             */
            foreach ($activeCarriers as $carrierCode => $carrierModel) {
                $carrierTitle = $carrierModel->getConfigData('title');
                $options[] = array('value' => $carrierCode, 'label' => $carrierTitle);
            }
            if ($options) {
                foreach ($options as $m) {
                    $this->addOption($m['value'], $m['label']);
                }
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}