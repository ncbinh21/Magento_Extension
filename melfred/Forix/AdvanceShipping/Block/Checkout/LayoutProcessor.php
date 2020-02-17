<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 18:11
 */

namespace Forix\AdvanceShipping\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    protected $_scopeConfig;
    protected $_layout;
    protected $_registry;


    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_layout = $layout;
        $this->_registry = $registry;
    }


    protected function processShippingNote($shippingNoteInfoElement)
    {
        /**
         * @var $blockNote \Magento\Cms\Block\Block
         */
        if ($shippingNote = $this->_scopeConfig->getValue('shipping/advance_shipping/checkout_cms_shipping_note')) {

            $blockNote = $this->_layout->createBlock(\Magento\Cms\Block\Block::class, 'checkout_cms_footer_shipping')->setBlockId($shippingNote);
            $noteHtml = $blockNote->toHtml();
            $shippingNoteInfoElement['config']['shippingNote']['note'] = nl2br($noteHtml);
        }
        if ($shippingHeavyNote = $this->_scopeConfig->getValue('shipping/advance_shipping/shipping_heavy_item_note')) {
            $blockNote = $this->_layout->createBlock(\Magento\Cms\Block\Block::class, 'shipping_heavy_item_note')->setBlockId($shippingHeavyNote);
            $noteHtml = $blockNote->toHtml();
            $shippingNoteInfoElement['config']['shippingNote']['heavy']  = nl2br($noteHtml);
	        $current_quote = $this->_registry->registry('current_quote');

	        $isHeavy = false;
		    if ($current_quote) {
			    $heavyNum = $this->_scopeConfig->getValue("forix_catalog/heavy/weight");
			    $items = $current_quote->getAllItems();
			    foreach ($items as $_item) {
				    if ($_item->getWeight() > $heavyNum) {
					    $isHeavy =  true;
					    break;
				    }
			    }
		    }

	        $shippingNoteInfoElement['config']['is_heavy']  = $isHeavy;
        }
        return $shippingNoteInfoElement;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        $shippingAddressConfig = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['config'];
        $shippingAddressConfig['shippingMethodItemTemplate'] = 'Forix_AdvanceShipping/shipping-address/shipping-method-item';
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['config'] = $shippingAddressConfig;

        $shippingNoteElement = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping_note_info'];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping_note_info'] = $this->processShippingNote($shippingNoteElement);

        return $jsLayout;
    }
}