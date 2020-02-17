<?php

namespace Forix\CustomerOrder\Block\Link;

class Render extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Forix\CustomerOrder\Helper\Data
     */
    protected $helperData;

    /**
     * Render constructor.
     * @param \Forix\CustomerOrder\Helper\Data $helperData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Forix\CustomerOrder\Helper\Data $helperData,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        $responseHtml = null; //  need to return at-least null
        if($this->helperData->isDistributorManager()) {
            $responseHtml = parent::_toHtml(); //Return link html
        }
        return $responseHtml;
    }
}